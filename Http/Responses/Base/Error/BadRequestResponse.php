<?php


namespace Modules\PublicAPI\Http\Responses\Base\Error;


use Modules\PublicAPI\Http\Responses\AbstractResponse;
use Modules\PublicAPI\Http\ResponseStatuses\AbstractResponseStatus;
use Modules\PublicAPI\Http\ResponseStatuses\Base\ErrorResponseStatus;

final class BadRequestResponse extends AbstractResponse
{
	/**
	 * BadRequestResponse constructor.
	 * @param \Modules\PublicAPI\Http\ResponseStatuses\AbstractResponseStatus|null $status
	 */
	public function __construct(AbstractResponseStatus $status = null)
	{
		$status = $status ?? new ErrorResponseStatus('bad_request', 'Bad Request');

		parent::__construct(400, $status);
	}
}