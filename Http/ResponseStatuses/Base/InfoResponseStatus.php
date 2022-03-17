<?php


namespace Modules\PublicAPI\Http\ResponseStatuses\Base;


use Modules\PublicAPI\Http\ResponseStatuses\AbstractResponseStatus;

class InfoResponseStatus extends AbstractResponseStatus
{
	public function type(): string
	{
		return 'info';
	}
}