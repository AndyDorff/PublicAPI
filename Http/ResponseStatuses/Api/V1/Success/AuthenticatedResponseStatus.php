<?php


namespace Modules\PublicAPI\Http\ResponseStatuses\Api\V1\Success;


use Modules\PublicAPI\Http\ResponseStatuses\Base\SuccessResponseStatus;

class AuthenticatedResponseStatus extends SuccessResponseStatus
{
	public function __construct(string $token, int $expiresTimestamp)
	{
		$data = [
			'access_token' => [
				'token' => $token,
				'expires_date' => $expiresTimestamp
			]
		];

		parent::__construct('authenticated', 'Authenticated', $data);
	}
}