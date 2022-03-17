<?php


namespace Modules\PublicAPI\Http\Responses\Base\Error;


use Modules\PublicAPI\Http\Responses\AbstractResponse;
use Modules\PublicAPI\Http\ResponseStatuses\AbstractResponseStatus;
use Modules\PublicAPI\Http\ResponseStatuses\Base\ErrorResponseStatus;

class UnauthorizedResponse extends AbstractResponse
{
	const AUTH_SCHEME_BEARER = 'bearer';

	private $authSchemes = [];

	public function __construct(array $authSchemes, AbstractResponseStatus $status = null)
	{
		$status = $status ?? new ErrorResponseStatus('unauthorized', 'Unauthorized');

		parent::__construct(401, $status);
		$this->setAuthSchemes($authSchemes);
	}

	protected function setAuthSchemes(array $authSchemes): void
	{
		$this->authSchemes = $authSchemes;
	}

	public function authSchemes(): array
	{
		return $this->authSchemes;
	}
}