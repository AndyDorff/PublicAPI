<?php


namespace Modules\PublicAPI\Http\ResponseStatuses\Api\V1\Error;


use Modules\PublicAPI\Http\ResponseStatuses\Base\ErrorResponseStatus;

final class NoTokenResponseStatus extends ErrorResponseStatus
{
	public function __construct(array $data = [])
	{
		parent::__construct('no_token', 'Access Token Not Provided', $data);
	}
}