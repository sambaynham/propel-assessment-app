<?php

namespace App\Providers;

use App\Services\Address\Domain\AddressInterface;
use App\Services\Address\Infrastructure\AddressRepository;
use App\Services\Address\Infrastructure\AddressRepositoryInterface;
use App\Services\Address\Infrastructure\AddressSearchInterface;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Application;
class AddressProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(AddressRepositoryInterface::class, function (Application $app) {
            return new AddressRepository($app->get(Filesystem::class));
        });
        $this->app->singleton(AddressSearchInterface::class, function (Application $app) {
            return new AddressRepository($app->get(Filesystem::class));
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
