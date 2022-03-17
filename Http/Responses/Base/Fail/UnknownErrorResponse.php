<?php


namespace Modules\PublicAPI\Http\Responses\Base\Fail;


use Modules\PublicAPI\Http\Responses\AbstractResponse;
use Modules\PublicAPI\Http\ResponseStatuses\AbstractResponseStatus;
use Modules\PublicAPI\Http\ResponseStatuses\Base\FailResponseStatus;

class UnknownErrorResponse extends AbstractResponse
{
	public function __construct(AbstractResponseStatus $status = null)
	{
		$status = $status ?? new FailResponseStatus('unknown_server_error', 'Unknown Server Error');

		parent::__construct(520, $status);
	}
}