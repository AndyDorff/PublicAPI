<?php


namespace Modules\PublicAPI\Http\ResponseStatuses\Api\V1\Error;


use Illuminate\Contracts\Support\MessageBag;
use Modules\PublicAPI\Http\ResponseStatuses\Base\ErrorResponseStatus;

final class ValidationErrorResponseStatus extends ErrorResponseStatus
{
	public function __construct(array $validationErrors, array $data = [])
	{
		$data['errors'] = $validationErrors;

		parent::__construct('validation_error', 'Validation Error', $data);
	}

	/**
	 * @param MessageBag $errors
	 * @param array $data
	 * @return static
	 */
	public static function fromMessageBag(MessageBag $errors, array $data = [])
	{
		return new static($errors->all(), $data);
	}

	public function errors(): array
	{
		return $this->data()['errors'];
	}
}