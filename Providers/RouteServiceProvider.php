<?php

namespace Modules\PublicAPI\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Modules\PublicAPI\Http\Middleware\Api\V1\RegisterPublicApiAddictionsMiddleware;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The module namespace to assume when generating URLs to actions.
     *
     * @var string
     */
    protected $moduleNamespace = 'Modules\PublicAPI\Http\Controllers';

    /**
     * Called before routes are registered.
     *
     * Register any model bindings or pattern based filters.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();
        $this->mapWebRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->moduleNamespace)
            ->group(module_path('PublicAPI', '/Routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        foreach(config('publicapi.base_paths', []) as $prefix => $as){
            Route::prefix($prefix)
                ->middleware([
                    RegisterPublicApiAddictionsMiddleware::class
                ])
                ->as($as)
                ->namespace($this->moduleNamespace)
                ->group(module_path('PublicAPI', '/Routes/api.php'));
        }
    }
}
