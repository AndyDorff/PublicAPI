<?php


namespace Modules\PublicAPI\Http\ResponseStatuses\Api\V1\Fail;


use Modules\PublicAPI\Http\ResponseStatuses\Base\FailResponseStatus;

class UnknownErrorResponseStatus extends FailResponseStatus
{
	public function __construct(array $data = [])
	{
		parent::__construct('unknown_server_error', 'Unknown Server Error', $data);
	}
}