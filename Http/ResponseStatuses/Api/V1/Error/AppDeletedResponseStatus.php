<?php


namespace Modules\PublicAPI\Http\ResponseStatuses\Api\V1\Error;


use Modules\PublicAPI\Http\ResponseStatuses\Base\ErrorResponseStatus;

class AppDeletedResponseStatus extends ErrorResponseStatus
{
    public function __construct(array $data = [])
    {
        parent::__construct('app_deleted', 'The application has been removed', $data);
    }
}
