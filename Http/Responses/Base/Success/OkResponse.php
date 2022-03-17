<?php


namespace Modules\PublicAPI\Http\Responses\Base\Success;


use Modules\PublicAPI\Http\Responses\AbstractResponse;
use Modules\PublicAPI\Http\ResponseStatuses\AbstractResponseStatus;
use Modules\PublicAPI\Http\ResponseStatuses\Base\SuccessResponseStatus;

class OkResponse extends AbstractResponse
{
	public function __construct(AbstractResponseStatus $status)
	{
		$status = $status ?? new SuccessResponseStatus('ok', 'OK');

		parent::__construct(200, $status);
	}
}