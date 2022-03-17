<?php

namespace Modules\PublicAPI\Http\Controllers\Api\V1;

use Modules\PublicAPI\Domain\Application\ApplicationKey;
use Modules\PublicAPI\Domain\Application\Interfaces\ApplicationsRepositoryInterface;
use Modules\PublicAPI\Http\Requests\Api\V1\AuthRequest;
use Modules\PublicAPI\Http\Responses\Base\Error\UnauthorizedResponse;
use Modules\PublicAPI\Http\ResponseStatuses\Api\V1\Error\AppNotFoundResponseStatus;
use Modules\PublicAPI\Http\ResponseStatuses\Api\V1\Error\AppSuspendedResponseStatus;
use Modules\PublicAPI\Http\ResponseStatuses\Api\V1\Error\InvalidTokenResponseStatus;
use Modules\PublicAPI\Http\ResponseStatuses\Api\V1\Error\NoTokenResponseStatus;
use Modules\PublicAPI\Http\ResponseStatuses\Api\V1\Error\TokenCompletelyExpiredResponseStatus;
use Modules\PublicAPI\Http\ResponseStatuses\Api\V1\Success\AuthenticatedResponseStatus;
use Modules\PublicAPI\Http\ResponseStatuses\Api\V1\Success\TokenReceivedResponseStatus;
use Modules\PublicAPI\Http\ResponseStatuses\Api\V1\Success\TokenRefreshedResponseStatus;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\JWTAuth;

class AuthController extends AbstractController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function authenticate(AuthRequest $request)
    {
        $response = $this->checkApplication($request->appKey());
        if(!$response){
            $token = auth()->attempt($request->credentials());
            if ($token) {
                $expiresDate = auth()->payload()->get('exp');
                $response = response()->success()->ok(new AuthenticatedResponseStatus($token, $expiresDate));
            }
            else{
                $response = response()->error()->notFound(new AppNotFoundResponseStatus());
            }
        }

        return $response;
    }

    private function checkApplication(string $appKey): ?Response
    {
        $applications = app(ApplicationsRepositoryInterface::class);
        $app = $applications->find(new ApplicationKey($appKey));
        if($app){
            if(!$app->isActive()) {
                $response = response()->error()->forbidden(new AppSuspendedResponseStatus());
            }
        }

        return $response ?? null;
    }

    public function getToken()
    {
        $auth = $this->auth();

        $exp = $auth->payload()->get('exp');
    	$token = $auth->getToken()->get();

    	return response()->success()->ok(new TokenReceivedResponseStatus($token, $exp));
    }

    public function refreshToken()
    {
        if (! $this->auth()->parser()->hasToken()) {
            return response()->error()->unauthorizedResponse(
                [UnauthorizedResponse::AUTH_SCHEME_BEARER],
                new NoTokenResponseStatus()
            );
        }

        try {
            $token = $this->auth()->parseToken()->refresh();
            $exp = $this->auth()->setToken($token)->payload()->get('exp');
        } catch (TokenExpiredException $e){
            return response()->error()->unauthorizedResponse(
                [UnauthorizedResponse::AUTH_SCHEME_BEARER],
                new TokenCompletelyExpiredResponseStatus()
            );
        } catch (JWTException $e){
            return response()->error()->unauthorizedResponse(
                [UnauthorizedResponse::AUTH_SCHEME_BEARER],
                new InvalidTokenResponseStatus()
            );
        }

        return response()->success()->ok(new TokenRefreshedResponseStatus($token, $exp));
    }

    /**
     * @return JWTAuth
     */
    private function auth()
    {
        return app('tymon.jwt.auth');
    }

}
