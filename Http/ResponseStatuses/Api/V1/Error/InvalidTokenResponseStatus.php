<?php


namespace Modules\PublicAPI\Http\ResponseStatuses\Api\V1\Error;


use Modules\PublicAPI\Http\ResponseStatuses\Base\ErrorResponseStatus;

final class InvalidTokenResponseStatus extends ErrorResponseStatus
{
	public function __construct(array $data = [])
	{
		parent::__construct('invalid_token', 'Invalid Access Token', $data);
	}
}