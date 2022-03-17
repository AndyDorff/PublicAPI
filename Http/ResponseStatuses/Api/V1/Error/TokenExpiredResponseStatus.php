<?php


namespace Modules\PublicAPI\Http\ResponseStatuses\Api\V1\Error;


use Modules\PublicAPI\Http\ResponseStatuses\Base\ErrorResponseStatus;

class TokenExpiredResponseStatus extends ErrorResponseStatus
{
	public function __construct(array $data = [])
	{
		parent::__construct('access_token_expired', 'Access Token has expired', $data);
	}
}