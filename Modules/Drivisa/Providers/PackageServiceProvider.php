<?php

namespace Modules\Drivisa\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Drivisa\Entities\Package;
use Modules\Drivisa\Entities\PackageData;
use Modules\Drivisa\Entities\PackageType;
use Modules\Drivisa\Repositories\Eloquent\EloquentPackageDataRepository;
use Modules\Drivisa\Repositories\Eloquent\EloquentPackageRepository;
use Modules\Drivisa\Repositories\Eloquent\EloquentPackageTypeRepository;
use Modules\Drivisa\Repositories\PackageDataRepository;
use Modules\Drivisa\Repositories\PackageRepository;
use Modules\Drivisa\Repositories\PackageTypeRepository;

class PackageServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerPackageBindings();
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

    protected function registerPackageBindings()
    {
        // Package type binding
        $this->app->bind(
            PackageTypeRepository::class,
            function () {
                return new EloquentPackageTypeRepository(new PackageType());
            }
        );

        // package binding
        $this->app->bind(
            PackageRepository::class,
            function () {
                return new EloquentPackageRepository(new Package());
            }
        );

        // package data binding
        $this->app->bind(
            PackageDataRepository::class,
            function () {
                return new EloquentPackageDataRepository(new PackageData());
            }
        );
    }
}
