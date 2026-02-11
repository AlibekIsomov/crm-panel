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
    }

    protected function loadModuleRoutes()
    {
        $modules = ['Catalog', 'Inventory'];

        foreach ($modules as $module) {
            $routeFile = app_path("Modules/{$module}/routes.php");

            if (file_exists($routeFile)) {
                Route::middleware('api')
                    ->group($routeFile);
            }
        }
    }
}
