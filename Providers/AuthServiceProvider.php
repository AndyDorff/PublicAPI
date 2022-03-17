<?php

namespace Modules\PublicAPI\Providers;

use Auth;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Modules\PublicAPI\Domain\Application\Interfaces\ApplicationsRepositoryInterface;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot()
    {
        $this->registerPolicies();
        Auth::provider('applications', function ($app, array $config) {
            return new ApplicationsUserProvider($app[ApplicationsRepositoryInterface::class]);
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
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
