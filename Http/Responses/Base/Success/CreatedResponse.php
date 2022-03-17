<?php


namespace Modules\PublicAPI\Http\Responses\Base\Success;


use Modules\PublicAPI\Http\Responses\AbstractResponse;
use Modules\PublicAPI\Http\ResponseStatuses\AbstractResponseStatus;
use Modules\PublicAPI\Http\ResponseStatuses\Base\SuccessResponseStatus;

class CreatedResponse extends AbstractResponse
{
    public function __construct(AbstractResponseStatus $status)
    {
        $status = $status ?? new SuccessResponseStatus('created', 'CREATED');

        parent::__construct(201, $status);
    }

}