<?php


namespace Modules\PublicAPI\Http\Responses\Base\Error;


use Modules\PublicAPI\Http\Responses\AbstractResponse;
use Modules\PublicAPI\Http\ResponseStatuses\AbstractResponseStatus;
use Modules\PublicAPI\Http\ResponseStatuses\Base\ErrorResponseStatus;

final class MethodNotAllowedResponse extends AbstractResponse
{
    /**
     * @var string
     */
    private $currentMethod;
    /**
     * @var string
     */
    private $expectedMethod;

    public function __construct(string $currentMethod, string $expectedMethod, AbstractResponseStatus $status = null)
	{
        $this->currentMethod = strtoupper($currentMethod);
        $this->expectedMethod = strtoupper($expectedMethod);
		$status = $status ?? new ErrorResponseStatus(
		    'method_not_allowed',
                'The '.$currentMethod.' method is not supported for this route. Supported methods: '.$this->expectedMethod.'.'
            );

		parent::__construct(405, $status);
    }

    public function currentMethod(): string
    {
        return $this->currentMethod;
    }

    public function expectedMethod(): string
    {
        return $this->expectedMethod;
    }
}