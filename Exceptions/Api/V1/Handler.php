<?php


namespace Modules\PublicAPI\Exceptions\Api\V1;


use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Modules\PublicAPI\Http\Responses\Base\Error\UnauthorizedResponse;
use Modules\PublicAPI\Http\ResponseStatuses\Api\V1\Error\InvalidTokenResponseStatus;
use Modules\PublicAPI\Http\ResponseStatuses\Api\V1\Error\NoTokenResponseStatus;
use Modules\PublicAPI\Http\ResponseStatuses\Api\V1\Error\TokenExpiredResponseStatus;
use Modules\PublicAPI\Http\ResponseStatuses\Api\V1\Error\ValidationErrorResponseStatus;
use Modules\PublicAPI\Http\ResponseStatuses\Api\V1\Fail\UnknownErrorResponseStatus;
use Modules\PublicAPI\Http\ResponseStatuses\Base\ErrorResponseStatus;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class Handler extends \Illuminate\Foundation\Exceptions\Handler
{
    /**
     * @param Request $request
     * @param \Exception $exception
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response|void
     */
    public function render($request, \Exception $exception)
    {
        $response = $this->doRender($request, $exception);
        if(!$response){
            $response = response()->fail()->unknownError(new UnknownErrorResponseStatus($this->getData($exception)));
        }

        return $response;
    }

    protected function doRender($request, \Exception $exception)
    {
        $data = $this->getData($exception);
        if($exception instanceof ValidationException){
            return response()->error()->unprocessableEntity(new ValidationErrorResponseStatus(
                $exception->errors(),
                $data
            ));
        }
        elseif($exception instanceof UnauthorizedHttpException){
            switch(true){
                case ($exception->getMessage() === 'Token not provided'):
                    return response()->error()->unauthorizedResponse(
                        [UnauthorizedResponse::AUTH_SCHEME_BEARER],
                        new NoTokenResponseStatus($data)
                    );
                case (
                    ($e = $exception->getPrevious())
                    && in_array(get_class($e), [TokenExpiredException::class, TokenInvalidException::class])
                ):
                    return $this->doRender($request, $e);
                default:
                    return response()->error()->unauthorizedResponse(
                        [UnauthorizedResponse::AUTH_SCHEME_BEARER],
                        new ErrorResponseStatus('unauthorized', $exception->getMessage(), $data)
                    );
            }
        }
        elseif($exception instanceof MethodNotAllowedHttpException){
            /**
             * @var Request $request
             */
            return response()->error()->methodNotAllowed(
                $request->method(), $exception->getHeaders()['Allow']
            );
        }
        elseif($exception instanceof HttpException){
            $httpStatus = $exception->getStatusCode();
            if($httpStatus >= 400 && $httpStatus < 500){
                return response()->error()->badRequest(
                    new ErrorResponseStatus('unknown_client_error', 'Unknown Client Error', $data)
                );
            }
            else{
                return response()->fail()->unknownError(new UnknownErrorResponseStatus($data));
            }
        }
        elseif($exception instanceof TokenExpiredException) {
            return response()->error()->unauthorizedResponse(
                [UnauthorizedResponse::AUTH_SCHEME_BEARER],
                new TokenExpiredResponseStatus($data)
            );
        }
        elseif($exception instanceof TokenInvalidException) {
            return response()->error()->unauthorizedResponse(
                [UnauthorizedResponse::AUTH_SCHEME_BEARER],
                new InvalidTokenResponseStatus($data)
            );
        }
        elseif($e = $exception->getPrevious()){
            return $this->doRender($request, $e);
        }
        else{
            return false;
        }
    }

    private function getData(\Throwable $exception, array $data = [])
    {
    	if(app()->runningUnitTests()){
    		$data['__exception_message'] = $exception->getMessage();
	    }

    	return $data;
    }
}
