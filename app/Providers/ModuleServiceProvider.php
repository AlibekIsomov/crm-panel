<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadModuleRoutes();
        $this->loadModuleMigrations();
    }

    protected function loadModuleMigrations()
    {
        $modules = ['Catalog', 'Inventory', 'Delivery'];

        foreach ($modules as $module) {
            $migrationPath = app_path("Modules/{$module}/Database/Migrations");

            if (is_dir($migrationPath)) {
                $this->loadMigrationsFrom($migrationPath);
            }
        }
    }

    protected function loadModuleRoutes()
    {
        $modules = ['Catalog', 'Inventory', 'Delivery'];

        foreach ($modules as $module) {
            $routeFile = app_path("Modules/{$module}/routes.php");

            if (file_exists($routeFile)) {
                Route::middleware('api')
                    ->group($routeFile);
            }
        }
    }
}
