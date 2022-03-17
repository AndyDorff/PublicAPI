<?php

namespace Modules\PublicAPI\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\PublicAPI\Factories\Response\Adapters\AbstractResponseAdapter;
use Modules\PublicAPI\Factories\Response\ResponseFactory;
use Modules\PublicAPI\Http\Responses\AbstractResponse;
use Modules\PublicAPI\Http\Responses\Api\V1\Response;
use Modules\PublicAPI\Http\Responses\Error\AbstractErrorResponse;
use Modules\PublicAPI\Http\Responses\Fail\AbstractFailResponse;
use Modules\PublicAPI\Http\Responses\Info\AbstractInfoResponse;
use Modules\PublicAPI\Http\Responses\Success\AbstractSuccessResponse;

class ResponseMacrosProvider extends ServiceProvider
{
	/**
	 * @var AbstractResponseAdapter
	 */
	private $responseAdapter;

	/**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
    	$this->responseAdapter = app(AbstractResponseAdapter::class);

    	$this->registerInfoMacros();
	    $this->registerSuccessMacros();
	    $this->registerErrorMacros();
	    $this->registerFailMacros();
	    $this->registerAdaptMacros();
    }

	private function registerInfoMacros(): void
	{
		$adapter = $this->responseAdapter;
		\Response::macro('info', function() use ($adapter){
			return app(ResponseFactory::class)->info()->withAdapter($adapter);
		});
	}

	private function registerSuccessMacros(): void
    {
	    $adapter = $this->responseAdapter;
	    \Response::macro('success', function() use ($adapter){
		    return app(ResponseFactory::class)->success()->withAdapter($adapter);
	    });
    }

    private function registerErrorMacros(): void
    {
	    $adapter = $this->responseAdapter;
	    \Response::macro('error', function() use ($adapter){
		    return app(ResponseFactory::class)->error()->withAdapter($adapter);
	    });
    }

	private function registerFailMacros(): void
	{
		$adapter = $this->responseAdapter;
		\Response::macro('fail', function () use ($adapter){
			return app(ResponseFactory::class)->fail()->withAdapter($adapter);
		});
	}

	private function registerAdaptMacros(): void
	{
		$adapter = $this->responseAdapter;
		\Response::macro('adapt', function (AbstractResponse $response) use ($adapter){
			return app(ResponseFactory::class)->adapt($adapter);
		});
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
