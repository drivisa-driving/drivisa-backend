<?php

namespace Modules\Translation\Providers;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Translation\Translator;
use Modules\Core\Events\LoadingBackendTranslations;
use Modules\Core\Traits\CanPublishConfiguration;
use Modules\Translation\Console\BuildTranslationsCacheCommand;
use Modules\Translation\Entities\Translation;
use Modules\Translation\Entities\TranslationTranslation;
use Modules\Translation\Repositories\Cache\CacheLocaleDecorator;
use Modules\Translation\Repositories\Cache\CacheTranslationDecorator;
use Modules\Translation\Repositories\Eloquent\EloquentLocaleRepository;
use Modules\Translation\Repositories\Eloquent\EloquentTranslationRepository;
use Modules\Translation\Repositories\File\FileTranslationRepository as FileDiskTranslationRepository;
use Modules\Translation\Repositories\FileTranslationRepository;
use Modules\Translation\Repositories\LocaleRepository;
use Modules\Translation\Repositories\TranslationRepository;
use Modules\Translation\Services\TranslationLoader;

class TranslationServiceProvider extends ServiceProvider
{
    use CanPublishConfiguration;

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerBindings();
        $this->registerConsoleCommands();

        $this->app['events']->listen(LoadingBackendTranslations::class, function (LoadingBackendTranslations $event) {
            $event->load('translations', Arr::dot(trans('translation::translations')));
            $event->load('locales', Arr::dot(trans('translation::locales')));
        });

        app('router')->bind('translations', function ($id) {
            return TranslationTranslation::find($id);
        });
    }

    private function registerBindings()
    {
        $this->app->bind(TranslationRepository::class, function () {
            $repository = new EloquentTranslationRepository(new Translation());

            return new CacheTranslationDecorator($repository);
        });

        $this->app->bind(FileTranslationRepository::class, function ($app) {
            return new FileDiskTranslationRepository($app['files'], $app['translation.loader']);
        });

        $this->app->bind(
            LocaleRepository::class,
            function () {
                $repository = new EloquentLocaleRepository();

                return new CacheLocaleDecorator($repository);
            }
        );
    }

    private function registerConsoleCommands()
    {
        $this->commands([
            BuildTranslationsCacheCommand::class,
        ]);
    }

    public function boot()
    {
        $this->publishConfig('translation', 'config');
        $this->publishConfig('translation', 'permissions');

        $this->registerValidators();
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        if ($this->app->runningInConsole() === true) {
            return;
        }

        if ($this->shouldRegisterCustomTranslator()) {
            $this->registerCustomTranslator();
        }
    }

    private function registerValidators()
    {
        Validator::extend('extensions', function ($attribute, $value, $parameters) {
            return in_array($value->getClientOriginalExtension(), $parameters);
        });

        Validator::replacer('extensions', function ($message, $attribute, $rule, $parameters) {
            return str_replace([':attribute', ':values'], [$attribute, implode(',', $parameters)], $message);
        });
    }

    /**
     * Should we register the Custom Translator?
     * @return bool
     */
    protected function shouldRegisterCustomTranslator()
    {
        if (false === config('app.translations-gui', true)) {
            return false;
        }

        if (false === env('INSTALLED', false)) {
            return false;
        }

        if (false === Schema::hasTable((new Translation)->getTable())) {
            return false;
        }

        return true;
    }

    protected function registerCustomTranslator()
    {
        $this->app->singleton('translation.loader', function ($app) {
            return new TranslationLoader($app['files'], $app['path.lang']);
        });
        $this->app->singleton('translator', function ($app) {
            $loader = $app['translation.loader'];

            $locale = $app['config']['app.locale'];

            $trans = new Translator($loader, $locale);

            $trans->setFallback($app['config']['app.fallback_locale']);

            return $trans;
        });
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
