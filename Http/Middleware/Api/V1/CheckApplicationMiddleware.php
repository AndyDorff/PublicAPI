<?php

namespace Modules\PublicAPI\Http\Middleware\Api\V1;

use Closure;
use Illuminate\Http\Request;
use Modules\PublicAPI\Domain\Application\ApplicationKey;
use Modules\PublicAPI\Domain\Application\Interfaces\ApplicationsRepositoryInterface;
use Modules\PublicAPI\Http\ResponseStatuses\Api\V1\Error\AppNotFoundResponseStatus;
use Modules\PublicAPI\Http\ResponseStatuses\Api\V1\Error\AppSuspendedResponseStatus;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Token;

class CheckApplicationMiddleware extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $this->auth->setRequest($request);
        if($token = $this->auth->parser()->parseToken()){
            $payload = $this->auth->getJWTProvider()->decode($token);
            $response = $this->checkApplication($payload['sub']);
        }

        return ($response ?? $next($request));
    }

    private function checkApplication(string $appKey)
    {
        /**
         * @var ApplicationsRepositoryInterface $applications
         */
        $applications = app(ApplicationsRepositoryInterface::class);
        $app = $applications->find(new ApplicationKey($appKey));
        if($app){
            if(!$app->isActive()) {
                $response = response()->error()->forbidden(new AppSuspendedResponseStatus());
            }
        }
        else{
            $response = response()->error()->notFound(new AppNotFoundResponseStatus());
        }

        return $response ?? null;
    }
}
