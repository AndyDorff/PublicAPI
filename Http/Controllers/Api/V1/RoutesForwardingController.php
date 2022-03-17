<?php


namespace Modules\PublicAPI\Http\Controllers\Api\V1;


use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\PublicAPI\Domain\Application\Application;
use Modules\PublicAPI\Http\Controllers\RoutesForwardingController as BaseRoutesForwardingController;

class RoutesForwardingController extends BaseRoutesForwardingController
{
    protected function getForwardingUrl(string $currentUrl, Request $request): string
    {
        /**
         * @var Application $app
         */
        $app = auth()->user()->getApplication();
        $version = Str::slug($app->version());

        return str_replace('/api/v1/', '/api/:'.$version.'/', $currentUrl);
    }
}
