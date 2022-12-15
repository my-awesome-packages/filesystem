<?php

namespace Awesome\Filesystem;

use Awesome\Filesystem\Contracts;
use Awesome\Filesystem\Models\File;
use Awesome\Filesystem\Repositories\FileRepository;
use Awesome\Filesystem\Services\FileService;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class ClientServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/migrations');

        $this->mergeConfigFrom(__DIR__ . '/../config/filedirectories.php', 'filedirectories');
    }

    public function register()
    {
        $this->app->bind(Contracts\FileRepository::class, function () {
            return new FileRepository(new File());
        });

        $this->app->bind(Contracts\FileService::class, FileService::class);
    }

    public function provides()
    {
        return [
            Contracts\FileRepository::class,
            Contracts\FileService::class
        ];
    }
}
