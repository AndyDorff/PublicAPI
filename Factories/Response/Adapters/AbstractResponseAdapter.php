<?php


namespace Modules\PublicAPI\Factories\Response\Adapters;


use Modules\PublicAPI\Http\Responses\AbstractResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractResponseAdapter
{
	public function adapt(AbstractResponse $response): Response
	{
		$obj = new \ReflectionObject($this);
		foreach($obj->getMethods(\ReflectionMethod::IS_PUBLIC) as $method){
			if($method->getNumberOfParameters() !== 1){
				continue;
			}
			$param = $method->getParameters()[0];
			if($param->getType()->getName() === get_class($response)){
				return $method->invoke($this, $response);
			}
		}

		return $this->doAdapt($response);
	}

	abstract protected function doAdapt(AbstractResponse $response, array $headers = []): Response;
}