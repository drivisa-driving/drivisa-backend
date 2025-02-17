<?php

namespace Modules\Media\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Modules\Core\Events\LoadingBackendTranslations;
use Modules\Core\Traits\CanPublishConfiguration;
use Modules\Media\Console\RefreshThumbnailCommand;
use Modules\Media\Contracts\DeletingMedia;
use Modules\Media\Contracts\StoringMedia;
use Modules\Media\Entities\File;
use Modules\Media\Events\FolderIsDeleting;
use Modules\Media\Events\FolderWasCreated;
use Modules\Media\Events\FolderWasUpdated;
use Modules\Media\Events\Handlers\CreateFolderOnDisk;
use Modules\Media\Events\Handlers\DeleteAllChildrenOfFolder;
use Modules\Media\Events\Handlers\DeleteFolderOnDisk;
use Modules\Media\Events\Handlers\HandleMediaStorage;
use Modules\Media\Events\Handlers\RemovePolymorphicLink;
use Modules\Media\Events\Handlers\RenameFolderOnDisk;
use Modules\Media\Image\ThumbnailManager;
use Modules\Media\Repositories\Eloquent\EloquentFileRepository;
use Modules\Media\Repositories\Eloquent\EloquentFolderRepository;
use Modules\Media\Repositories\FileRepository;
use Modules\Media\Repositories\FolderRepository;

class MediaServiceProvider extends ServiceProvider
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

        $this->registerCommands();

        $this->app['events']->listen(LoadingBackendTranslations::class, function (LoadingBackendTranslations $event) {
            $event->load('media', Arr::dot(trans('media::media')));
            $event->load('folders', Arr::dot(trans('media::folders')));
        });

        app('router')->bind('media', function ($id) {
            return app(FileRepository::class)->find($id);
        });
    }

    private function registerBindings()
    {
        $this->app->bind(FileRepository::class, function () {
            return new EloquentFileRepository(new File());
        });
        $this->app->bind(FolderRepository::class, function () {
            return new EloquentFolderRepository(new File());
        });
    }

    /**
     * Register all commands for this module
     */
    private function registerCommands()
    {
        $this->registerRefreshCommand();
    }

    /**
     * Register the refresh thumbnails command
     */
    private function registerRefreshCommand()
    {
        $this->app->singleton('command.media.refresh', function ($app) {
            return new RefreshThumbnailCommand($app['Modules\Media\Repositories\FileRepository']);
        });

        $this->commands('command.media.refresh');
    }

    public function boot(DispatcherContract $events)
    {
        $this->publishConfig('media', 'config');
        $this->publishConfig('media', 'permissions');
        $this->publishConfig('media', 'assets');

        $events->listen(StoringMedia::class, HandleMediaStorage::class);
        $events->listen(DeletingMedia::class, RemovePolymorphicLink::class);
        $events->listen(FolderWasCreated::class, CreateFolderOnDisk::class);
        $events->listen(FolderWasUpdated::class, RenameFolderOnDisk::class);
        $events->listen(FolderIsDeleting::class, DeleteFolderOnDisk::class);
        $events->listen(FolderIsDeleting::class, DeleteAllChildrenOfFolder::class);

        $this->registerThumbnails();

        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }

    private function registerThumbnails()
    {
        $this->app[ThumbnailManager::class]->registerThumbnail('mediumThumb', [
            'resize' => [
                'width' => 180,
                'height' => null,
                'callback' => function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                },
            ],
        ]);
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
