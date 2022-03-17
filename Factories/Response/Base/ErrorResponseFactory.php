<?php


namespace Modules\PublicAPI\Factories\Response\Base;


use Modules\PublicAPI\Factories\Response\Base\AbstractResponseFactory;
use Modules\PublicAPI\Http\Responses\AbstractResponse;
use Modules\PublicAPI\Http\Responses\Base\CustomResponse;
use Modules\PublicAPI\Http\Responses\Base\Error\ConflictResponse;
use Modules\PublicAPI\Http\Responses\Base\Error\ForbiddenResponse;
use Modules\PublicAPI\Http\Responses\Base\Error\MethodNotAllowedResponse;
use Modules\PublicAPI\Http\Responses\Base\Error\NotFoundResponse;
use Modules\PublicAPI\Http\ResponseStatuses\AbstractResponseStatus;
use Modules\PublicAPI\Http\Responses\Base\Error\BadRequestResponse;
use Modules\PublicAPI\Http\Responses\Base\Error\UnauthorizedResponse;
use Modules\PublicAPI\Http\Responses\Base\Error\UnprocessableEntityResponse;
use Modules\PublicAPI\Http\ResponseStatuses\Base\ErrorResponseStatus;

final class ErrorResponseFactory extends AbstractResponseFactory
{
	public function badRequest(AbstractResponseStatus $status = null)
	{
		return $this->return(new BadRequestResponse($status));
	}

	public function unauthorizedResponse(array $authSchemes, AbstractResponseStatus $status = null)
	{
		return $this->return(new UnauthorizedResponse($authSchemes, $status));
	}

	public function unprocessableEntity(AbstractResponseStatus $status = null)
	{
		return $this->return(new UnprocessableEntityResponse($status));
	}

	public function forbidden(AbstractResponseStatus $status = null)
    {
        return $this->return(new ForbiddenResponse($status));
    }

    public function notFound(AbstractResponseStatus $status = null)
    {
        return $this->return(new NotFoundResponse($status));
    }

    public function conflict(AbstractResponseStatus $status = null)
    {
        return $this->return(new ConflictResponse($status));
    }

    public function methodNotAllowed(string $currentMethod, string $expectedMethod, AbstractResponseStatus $status = null)
    {
        return $this->return(new MethodNotAllowedResponse($currentMethod, $expectedMethod, $status));
    }
}
