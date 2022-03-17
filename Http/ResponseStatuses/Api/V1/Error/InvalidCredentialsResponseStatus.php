<?php


namespace Modules\PublicAPI\Http\ResponseStatuses\Api\V1\Error;


use Modules\PublicAPI\Http\ResponseStatuses\Base\ErrorResponseStatus;

final class InvalidCredentialsResponseStatus extends ErrorResponseStatus
{
	public function __construct(array $errors)
	{
		$data['errors'] = array_intersect_key($errors, [
			'app_key' => [],
			'app_secret' => []
		]);

		parent::__construct('invalid_credentials', 'Invalid credentials', $data);
	}

	public function errors(): array
	{
		return $this->data()['errors'];
	}
}