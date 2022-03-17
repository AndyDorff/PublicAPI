<?php


namespace Modules\PublicAPI\Http\ResponseStatuses\Api\V1\Success;


use Modules\PublicAPI\Http\ResponseStatuses\Base\SuccessResponseStatus;

class TokenReceivedResponseStatus extends SuccessResponseStatus
{
    public function __construct(string $token, int $exp, array $data = [])
    {
        $data['token'] = $token;
        $data['expires_date'] = $exp;

        parent::__construct('token_received', 'Token successfully received', $data);
    }
}
