<?php


namespace Modules\PublicAPI\Http\Responses\Base\Error;


use Modules\PublicAPI\Http\Responses\AbstractResponse;
use Modules\PublicAPI\Http\ResponseStatuses\AbstractResponseStatus;
use Modules\PublicAPI\Http\ResponseStatuses\Base\ErrorResponseStatus;

class ForbiddenResponse extends AbstractResponse
{
    public function __construct(AbstractResponseStatus $status = null)
    {
        $status = $status ?? new ErrorResponseStatus('forbidden', 'Forbidden');

        parent::__construct(403, $status);
    }
}
