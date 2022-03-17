<?php


namespace Modules\PublicAPI\Http\Responses\Base\Error;


use Modules\PublicAPI\Http\Responses\AbstractResponse;
use Modules\PublicAPI\Http\ResponseStatuses\AbstractResponseStatus;
use Modules\PublicAPI\Http\ResponseStatuses\Base\ErrorResponseStatus;

class UnprocessableEntityResponse extends AbstractResponse
{
	public function __construct(AbstractResponseStatus $status = null)
	{
		$status = $status ?? new ErrorResponseStatus('unprocessable_entity', 'Unprocessable Entity');

		parent::__construct(422, $status);
	}
}