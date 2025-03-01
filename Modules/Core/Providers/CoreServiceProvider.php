<?php

namespace Modules\Core\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Modules\Core\Console\DeleteModuleCommand;
use Modules\Core\Console\DownloadModuleCommand;
use Modules\Core\Console\InstallCommand;
use Modules\Core\Console\PublishModuleAssetsCommand;
use Modules\Core\Events\LoadingBackendTranslations;
use Modules\Core\Traits\CanPublishConfiguration;
use Nwidart\Modules\Module;

class CoreServiceProvider extends ServiceProvider
{
    use CanPublishConfiguration;

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * The filters base class name.
     *
     * @var array
     */
    protected $middleware = [
        'Core' => [
            'permissions' => 'PermissionMiddleware',
            'auth.admin' => 'AdminMiddleware',
            'public.checkLocale' => 'PublicMiddleware',
            'localizationRedirect' => 'LocalizationMiddleware',
            'localeSessionRedirect' => 'LocaleSessionRedirectMiddleware',
            'can' => 'Authorization',
            'localization' => 'Localization',
        ],
    ];
    protected $middlewareGroups = [
        'web' => [
        ],
        'api' => [
            'localization'
        ],
    ];


    public function boot()
    {
        $this->publishConfig('core', 'available-locales');
        $this->publishConfig('core', 'config');
        $this->publishConfig('core', 'core');
        $this->publishConfig('core', 'settings');
        $this->publishConfig('core', 'permissions');

        $this->registerMiddleware($this->app['router']);
        $this->pushMiddlewareToGroup($this->app['router']);


        $this->registerModuleResourceNamespaces();
    }

    /**
     * Register the filters.
     *
     * @param Router $router
     * @return void
     */
    public function registerMiddleware(Router $router)
    {
        foreach ($this->middleware as $module => $middlewares) {
            foreach ($middlewares as $name => $middleware) {
                $class = "Modules\\{$module}\\Http\\Middleware\\{$middleware}";

                $router->aliasMiddleware($name, $class);
            }
        }
    }

    public function pushMiddlewareToGroup(Router $router)
    {
        foreach ($this->middlewareGroups as $middlewareGroup => $group) {
            foreach ($group as $name => $middleware) {
                $router->pushMiddlewareToGroup($middlewareGroup, $middleware);
            }
        }
    }

    /**
     * Register the modules aliases
     */
    private function registerModuleResourceNamespaces()
    {
        foreach ($this->app['modules']->getOrdered() as $module) {
            $this->registerViewNamespace($module);
            $this->registerLanguageNamespace($module);
        }
    }

    /**
     * Register the view namespaces for the modules
     * @param Module $module
     */
    protected function registerViewNamespace(Module $module)
    {
        $hints = [];
        $moduleName = $module->getLowerName();

        if (is_core_module($moduleName)) {
            $configFile = 'config';
            $configKey = 'ceo.' . $moduleName . '.' . $configFile;

            $this->mergeConfigFrom($module->getExtraPath('Config' . DIRECTORY_SEPARATOR . $configFile . '.php'), $configKey);
            $moduleConfig = $this->app['config']->get($configKey . '.useViewNamespaces');

            if (Arr::get($moduleConfig, 'resources') === true) {
                $hints[] = base_path('resources/views/ceo/' . $moduleName);
            }
        }

        $hints[] = $module->getPath() . '/Resources/views';

        $this->app['view']->addNamespace($moduleName, $hints);
    }

    /**
     * Register the language namespaces for the modules
     * @param Module $module
     */
    protected function registerLanguageNamespace(Module $module)
    {
        $moduleName = $module->getLowerName();

        $langPath = base_path("resources/lang/$moduleName");
        $secondPath = base_path("resources/lang/translation/$moduleName");

        if ($moduleName !== 'translation' && $this->hasPublishedTranslations($langPath)) {
            $this->loadTranslationsFrom($langPath, $moduleName);
        }
        if ($this->hasPublishedTranslations($secondPath)) {
            $this->loadTranslationsFrom($secondPath, $moduleName);
        }
        if ($this->moduleHasCentralisedTranslations($module)) {
            $this->loadTranslationsFrom($this->getCentralisedTranslationPath($module), $moduleName);
        }

        $this->loadTranslationsFrom($module->getPath() . '/Resources/lang', $moduleName);
    }

    /**
     * @param string $path
     * @return bool
     */
    private function hasPublishedTranslations($path)
    {
        return is_dir($path);
    }

    /**
     * Does a Module have it's Translations centralised in the Translation module?
     * @param Module $module
     * @return bool
     */
    private function moduleHasCentralisedTranslations(Module $module)
    {
        return is_dir($this->getCentralisedTranslationPath($module));
    }

    /**
     * Get the absolute path to the Centralised Translations for a Module (via the Translations module)
     * @param Module $module
     * @return string
     */
    private function getCentralisedTranslationPath(Module $module)
    {
        $path = config('modules.paths.modules') . '/Translation';

        return $path . "/Resources/lang/{$module->getLowerName()}";
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('ceo.isInstalled', function () {
            return true === config('ceo.core.core.is_installed');
        });

        $this->registerCommands();
        $this->registerServices();
        $this->setLocalesConfigurations();

        $this->app['events']->listen(LoadingBackendTranslations::class, function (LoadingBackendTranslations $event) {
            $event->load('core', Arr::dot(trans('core::core')));
        });
    }

    /**
     * Register the console commands
     */
    private function registerCommands()
    {
        $this->commands([
            InstallCommand::class,
            PublishModuleAssetsCommand::class,
            DownloadModuleCommand::class,
            DeleteModuleCommand::class,
        ]);
    }

    private function registerServices()
    {
        $this->app->singleton('ceo.ModulesList', function () {
            return [
                'core',
                'media',
                'setting',
                'translation',
                'user',
                'workshop',
            ];
        });
    }

    /**
     * Set the locale configuration for
     * - laravel localization
     * - laravel translatable
     */
    private function setLocalesConfigurations()
    {
        if ($this->app['ceo.isInstalled'] === false || $this->app->runningInConsole() === true) {
            return;
        }

        $localeConfig = $this->app['cache']
            ->tags('setting.settings', 'global')
            ->remember(
                'ceo.locales',
                120,
                function () {
                    return DB::table('setting__settings')->whereName('core::locales')->first();
                }
            );
        if ($localeConfig) {
            $locales = json_decode($localeConfig->plainValue);
            $availableLocales = [];
            foreach ($locales as $locale) {
                $availableLocales = array_merge($availableLocales, [$locale => config("available-locales.$locale")]);
            }

            $laravelDefaultLocale = $this->app->config->get('app.locale');

            if (!in_array($laravelDefaultLocale, array_keys($availableLocales))) {
                $this->app->config->set('app.locale', array_keys($availableLocales)[0]);
            }
            $this->app->config->set('laravellocalization.supportedLocales', $availableLocales);
            $this->app->config->set('translatable.locales', $locales);
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
