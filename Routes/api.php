<?php

use Illuminate\Routing\Router;
use Modules\PublicAPI\Http\Controllers\Api\V1\AuthController;
use Modules\PublicAPI\Http\Controllers\Api\V1\RoutesForwardingController;
use Modules\PublicAPI\Http\Middleware\Api\V1\CheckApplicationMiddleware;
use Tymon\JWTAuth\Http\Middleware\Authenticate;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::group([
    'prefix' => 'v1',
    'as' => 'v1.'
], function(Router $router){

    //$router->get('/test', function())
    $router->post('/auth', [AuthController::class, 'authenticate'])->name('auth');
    $router->group(['middleware' => CheckApplicationMiddleware::class], function(Router $router){

        $router->put('/auth/token', [AuthController::class, 'refreshToken'])->name('auth.token.refresh');
        $router->group(['middleware' => Authenticate::class], function (Router $router){

            $router->get('/auth/token', [AuthController::class, 'getToken'])->name('auth.token');

            $router->any('/{rel}', [RoutesForwardingController::class, 'forward'])
                ->name('fw.routes')
                ->where('rel',  '(.+)');
        });
    });
});

Route::get('/:v1/forwarding/to/another/route', function(\Illuminate\Http\Request $request){
    return response()->success()->ok();
});
