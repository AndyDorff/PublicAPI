<?php


namespace Modules\PublicAPI\Http\ResponseStatuses\Base;


use Modules\PublicAPI\Http\ResponseStatuses\AbstractResponseStatus;

class FailResponseStatus extends AbstractResponseStatus
{
	public function type(): string
	{
		return 'fail';
	}
}