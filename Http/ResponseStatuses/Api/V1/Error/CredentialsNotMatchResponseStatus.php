<?php


namespace Modules\PublicAPI\Http\ResponseStatuses\Api\V1\Error;


use Modules\PublicAPI\Http\ResponseStatuses\Base\ErrorResponseStatus;

final class CredentialsNotMatchResponseStatus extends ErrorResponseStatus
{
	public function __construct(string $appKey, string $appSecret)
	{
		$data = [
			'app_key' => $appKey,
			'app_secret' => $appSecret
		];

		parent::__construct('credentials_not_match', 'Application credentials dont match', $data);
	}
}