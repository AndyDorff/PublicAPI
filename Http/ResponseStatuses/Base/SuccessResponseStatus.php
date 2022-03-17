<?php


namespace Modules\PublicAPI\Http\ResponseStatuses\Base;


use Modules\PublicAPI\Http\ResponseStatuses\AbstractResponseStatus;

class SuccessResponseStatus extends AbstractResponseStatus
{
	public function type(): string
	{
		return 'success';
	}
}