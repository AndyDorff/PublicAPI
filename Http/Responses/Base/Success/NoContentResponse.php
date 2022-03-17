<?php


namespace Modules\PublicAPI\Http\Responses\Base\Success;


use Modules\PublicAPI\Http\Responses\AbstractResponse;
use Modules\PublicAPI\Http\ResponseStatuses\AbstractResponseStatus;
use Modules\PublicAPI\Http\ResponseStatuses\Base\SuccessResponseStatus;

class NoContentResponse extends AbstractResponse
{
    public function __construct(AbstractResponseStatus $status)
    {
        $status = $status ?? new SuccessResponseStatus('no_content', 'NO CONTENT');

        parent::__construct(204, $status);
    }

}