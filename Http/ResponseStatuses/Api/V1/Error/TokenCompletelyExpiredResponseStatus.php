<?php


namespace Modules\PublicAPI\Http\ResponseStatuses\Api\V1\Error;


use Modules\PublicAPI\Http\ResponseStatuses\Base\ErrorResponseStatus;

class TokenCompletelyExpiredResponseStatus extends ErrorResponseStatus
{
    public function __construct(array $data = [])
    {
        parent::__construct('refresh_token_expired', 'Token has expired and can no longer be refreshed', $data);
    }
}
