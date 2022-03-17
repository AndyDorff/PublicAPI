<?php


namespace Modules\PublicAPI\Http\Responses\Base\Fail;


use Modules\PublicAPI\Http\Responses\AbstractResponse;
use Modules\PublicAPI\Http\Responses\Fail\FailResponseStatus;
use Modules\PublicAPI\Http\ResponseStatuses\AbstractResponseStatus;

class InternalServerErrorResponse extends AbstractResponse
{
	public function __construct(AbstractResponseStatus $status = null)
	{
		$status = $status ?? new FailResponseStatus('internal_server_error', 'Internal Server Error');

		parent::__construct(500, $status);
	}
}