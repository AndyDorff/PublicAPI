<?php

namespace Modules\PublicAPI\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

abstract class RoutesForwardingController extends Controller
{
    public function forward(Request $request)
    {
        $requestUri = $this->getForwardingUrl($request->server->get('REQUEST_URI'), $request);
        $request = $request->duplicate();
        $request->server->set('REQUEST_URI', $requestUri);

        return \Route::dispatch($request);
    }

    abstract protected function getForwardingUrl(string $currentUrl, Request $request): string;
}
