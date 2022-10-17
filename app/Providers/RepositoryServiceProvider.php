<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('App\Repositories\RepositoryInterface', 'App\Repositories\EmployeeRepository');
        $this->app->bind('App\Repositories\RepositoryInterface', 'App\Repositories\AuthRepository');
        $this->app->bind('App\Repositories\ExportImportInterface', 'App\Repositories\AuthRepository');
    }
}
