<?php

namespace Modules\Workshop\Providers;

use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Modules\Core\Events\LoadingBackendTranslations;
use Modules\Core\Services\Composer;
use Modules\Core\Traits\CanPublishConfiguration;
use Modules\Workshop\Console\EntityScaffoldCommand;
use Modules\Workshop\Console\ModuleScaffoldCommand;
use Modules\Workshop\Console\UpdateModuleCommand;
use Modules\Workshop\Scaffold\Module\Generators\EntityGenerator;
use Modules\Workshop\Scaffold\Module\Generators\FilesGenerator;
use Modules\Workshop\Scaffold\Module\Generators\ValueObjectGenerator;
use Modules\Workshop\Scaffold\Module\ModuleScaffold;
use Nwidart\Modules\Contracts\RepositoryInterface;

class WorkshopServiceProvider extends ServiceProvider
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
        $this->registerCommands();

        $this->app['events']->listen(LoadingBackendTranslations::class, function (LoadingBackendTranslations $event) {
            $event->load('workshop', Arr::dot(trans('workshop::workshop')));
            $event->load('modules', Arr::dot(trans('workshop::modules')));
        });

        app('router')->bind('module', function ($module) {
            return app(RepositoryInterface::class)->find($module);
        });
    }

    /**
     * Register artisan commands
     */
    private function registerCommands()
    {
        $this->registerModuleScaffoldCommand();
        $this->registerUpdateCommand();

        $this->commands([
            'command.ceo.module.scaffold',
            'command.ceo.module.update',
            EntityScaffoldCommand::class,
        ]);
    }

    /**
     * Register the scaffold command
     */
    private function registerModuleScaffoldCommand()
    {
        $this->app->singleton('ceo.module.scaffold', function ($app) {
            return new ModuleScaffold(
                $app['files'],
                $app['config'],
                new EntityGenerator($app['files'], $app['config']),
                new ValueObjectGenerator($app['files'], $app['config']),
                new FilesGenerator($app['files'], $app['config'])
            );
        });

        $this->app->singleton('command.ceo.module.scaffold', function ($app) {
            return new ModuleScaffoldCommand($app['ceo.module.scaffold']);
        });
    }

    /**
     * Register the update module command
     */
    private function registerUpdateCommand()
    {
        $this->app->singleton('command.ceo.module.update', function ($app) {
            return new UpdateModuleCommand(new Composer($app['files'], base_path()));
        });
    }

    public function boot()
    {
        $this->publishConfig('workshop', 'permissions');
        $this->publishConfig('workshop', 'config');
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }
}
