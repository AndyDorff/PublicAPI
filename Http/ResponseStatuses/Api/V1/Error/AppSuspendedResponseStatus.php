<?php


namespace Modules\PublicAPI\Http\ResponseStatuses\Api\V1\Error;


use Modules\PublicAPI\Http\ResponseStatuses\Base\ErrorResponseStatus;

class AppSuspendedResponseStatus extends ErrorResponseStatus
{
    public function __construct(array $data = [])
    {
        parent::__construct('app_suspended', 'The application has been suspended', $data);
    }
}
