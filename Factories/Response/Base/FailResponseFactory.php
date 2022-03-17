<?php


namespace Modules\PublicAPI\Factories\Response\Base;


use Modules\PublicAPI\Http\ResponseStatuses\AbstractResponseStatus;
use Modules\PublicAPI\Http\Responses\Base\Fail\InternalServerErrorResponse;
use Modules\PublicAPI\Http\Responses\Base\Fail\UnknownErrorResponse;

final class FailResponseFactory extends AbstractResponseFactory
{
	public function internalServerError(AbstractResponseStatus $status = null)
	{
		return $this->return(new InternalServerErrorResponse($status));
	}

	public function unknownError(AbstractResponseStatus $status = null)
	{
		return $this->return(new UnknownErrorResponse($status));
	}
}