<?php

namespace Modules\PublicAPI\Http\Middleware\Api\V1;

use Closure;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\Request;
use Modules\PublicAPI\Exceptions\Api\V1\Handler;
use Modules\PublicAPI\Providers\AuthServiceProvider;
use Modules\PublicAPI\Providers\ResponseMacrosProvider;
use Tymon\JWTAuth\Providers\LaravelServiceProvider;

class RegisterPublicApiAddictionsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Closure|callable  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $this->registerJWTEngine();
        $this->registerResponseMacros();
        $this->registerExceptionsHandler();

        return $next($request);
    }

    private function registerResponseMacros()
    {
    	\App::register(ResponseMacrosProvider::class);
    }

    private function registerExceptionsHandler()
    {
        \App::singleton(
            ExceptionHandler::class,
            Handler::class
        );
    }

    private function registerJWTEngine()
    {
        \App::register(AuthServiceProvider::class);

        auth()->setDefaultDriver('api/v1');
    }
}
