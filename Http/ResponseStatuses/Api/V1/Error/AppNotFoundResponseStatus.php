<?php


namespace Modules\PublicAPI\Http\ResponseStatuses\Api\V1\Error;


use Modules\PublicAPI\Http\ResponseStatuses\Base\ErrorResponseStatus;

class AppNotFoundResponseStatus extends ErrorResponseStatus
{
    public function __construct(array $data = [])
    {
        parent::__construct('app_not_found', 'Application not found', $data);
    }
}
