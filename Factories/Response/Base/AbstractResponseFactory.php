<?php


namespace Modules\PublicAPI\Factories\Response\Base;


use Modules\PublicAPI\Factories\Response\Adapters\AbstractResponseAdapter;
use Modules\PublicAPI\Http\Responses\AbstractResponse;
use Modules\PublicAPI\Http\Responses\Base\CustomResponse;
use Modules\PublicAPI\Http\ResponseStatuses\AbstractResponseStatus;

abstract class AbstractResponseFactory
{
	/**
	 * @var AbstractResponseAdapter
	 */
	private $adapter;

	/**
	 * @param AbstractResponseAdapter $adapter
	 * @return static
	 */
	public function withAdapter(AbstractResponseAdapter $adapter)
	{
		$factory = new static();
		$factory->adapter = $adapter;

		return $factory;
	}

    public function custom(int $code, $responseStatus)
    {
        return $this->return(new CustomResponse($code, $responseStatus));
    }

	/**
	 * @param AbstractResponse $response
	 * @return mixed
	 */
	protected function return(AbstractResponse $response)
	{
		return ( $this->adapter
			? $this->adapter->adapt($response)
			: $response
		);
	}

}