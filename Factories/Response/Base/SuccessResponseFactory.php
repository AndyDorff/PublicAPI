<?php


namespace Modules\PublicAPI\Factories\Response\Base;


use Modules\PublicAPI\Factories\Response\Base\AbstractResponseFactory;
use Modules\PublicAPI\Http\Responses\AbstractResponse;
use Modules\PublicAPI\Http\Responses\Base\CustomResponse;
use Modules\PublicAPI\Http\Responses\Base\Success\CreatedResponse;
use Modules\PublicAPI\Http\Responses\Base\Success\NoContentResponse;
use Modules\PublicAPI\Http\ResponseStatuses\AbstractResponseStatus;
use Modules\PublicAPI\Http\Responses\Base\Success\OkResponse;
use Modules\PublicAPI\Http\ResponseStatuses\Base\SuccessResponseStatus;

final class SuccessResponseFactory extends AbstractResponseFactory
{
	public function ok(AbstractResponseStatus $status = null)
	{
	    $status = $status ?? new SuccessResponseStatus('ok', 'OK');

		return $this->return(new OkResponse($status));
	}

	public function created(AbstractResponseStatus $status = null)
    {
        $status = $status ?? new SuccessResponseStatus('created', 'CREATED');

        return $this->return(new CreatedResponse($status));
    }

    public function noContent(AbstractResponseStatus $status = null)
    {
        $status = $status ?? new SuccessResponseStatus('no_content', 'NO CONTENT');

        return $this->return(new NoContentResponse($status));
    }
}
