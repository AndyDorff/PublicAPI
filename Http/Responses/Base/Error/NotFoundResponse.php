<?php


namespace Modules\PublicAPI\Http\Responses\Base\Error;


use Modules\PublicAPI\Http\Responses\AbstractResponse;
use Modules\PublicAPI\Http\ResponseStatuses\AbstractResponseStatus;
use Modules\PublicAPI\Http\ResponseStatuses\Base\ErrorResponseStatus;

class NotFoundResponse extends AbstractResponse
{
    public function __construct(AbstractResponseStatus $status = null)
    {
        $status = $status ?? new ErrorResponseStatus('not_found', 'Not Found');

        parent::__construct(404, $status);
    }
}
