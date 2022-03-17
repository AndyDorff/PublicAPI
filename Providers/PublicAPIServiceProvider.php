<?php

namespace Modules\PublicAPI\Providers;

use Illuminate\Contracts\Foundation\CachesConfiguration;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Modules\PublicAPI\Console\CreateApplication;
use Modules\PublicAPI\Console\CreatePersonalToken;
use Modules\PublicAPI\Domain\Application\Interfaces\ApplicationsRepositoryInterface;
use Modules\PublicAPI\Domain\Application\Repositories\EloquentApplicationsRepository;
use Modules\PublicAPI\Factories\Response\Adapters\AbstractResponseAdapter;
use Modules\PublicAPI\Factories\Response\Adapters\JsonResponseAdapter;
use Modules\PublicAPI\Factories\Response\ResponseFactory;

class PublicAPIServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'PublicAPI';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'publicapi';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
	    $this->app->register(RouteServiceProvider::class);
	    $this->app->register(AuthServiceProvider::class);
	    $this->app->register(JWTServiceProvider::class);

	    $this->app->singleton(ApplicationsRepositoryInterface::class, function(){
		    return new EloquentApplicationsRepository(\DB::connection());
	    });
	    $this->app->singleton(ResponseFactory::class);
	    $this->app->singleton(AbstractResponseAdapter::class, JsonResponseAdapter::class);

        //commands
        $this->commands([
            CreateApplication::class,
            CreatePersonalToken::class
        ]);
    }

    /**
     * Register config.
     *
     * @return void0
     */
    protected function registerConfig()
    {
        $this->publishes([
            module_path($this->moduleName, 'Config/config.php') => config_path($this->moduleNameLower . '.php'),
        ], 'config');
        foreach(['config' => $this->moduleNameLower, 'jwt' => 'jwt', 'auth' => 'auth'] as $config => $key){
            $this->mergeConfigFrom(
                module_path($this->moduleName, 'Config/'.$config.'.php'), $key
            );
        }
    }

    protected function mergeConfigFrom($path, $key)
    {
        if (! ($this->app instanceof CachesConfiguration && $this->app->configurationIsCached())) {
            $this->app['config']->set($key, array_replace_recursive(
                require $path, $this->app['config']->get($key, [])
            ));
        }
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/' . $this->moduleNameLower);

        $sourcePath = module_path($this->moduleName, 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ], ['views', $this->moduleNameLower . '-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/' . $this->moduleNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
        } else {
            $this->loadTranslationsFrom(module_path($this->moduleName, 'Resources/lang'), $this->moduleNameLower);
        }
    }

    /**
     * Register an additional directory of factories.
     *
     * @return void
     */
    public function registerFactories()
    {
        if (! app()->environment('production') && $this->app->runningInConsole()) {
            app(Factory::class)->load(module_path($this->moduleName, 'Database/factories'));
        }
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

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (\Config::get('view.paths') as $path) {
            if (is_dir($path . '/modules/' . $this->moduleNameLower)) {
                $paths[] = $path . '/modules/' . $this->moduleNameLower;
            }
        }
        return $paths;
    }
}
