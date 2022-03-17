<?php


namespace Modules\PublicAPI\Providers;


use Tymon\JWTAuth\Providers\AbstractServiceProvider;

final class JWTServiceProvider extends AbstractServiceProvider
{
    protected $middlewareAliases = [];

    public function boot()
    {
        $this->extendAuthGuard();
    }
}