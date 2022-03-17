<?php


namespace Modules\PublicAPI\Factories\Response\Adapters;


use Modules\PublicAPI\Http\Responses\AbstractResponse;
use Modules\PublicAPI\Http\Responses\Base\Error\MethodNotAllowedResponse;
use Modules\PublicAPI\Http\Responses\Base\Error\UnauthorizedResponse;
use Symfony\Component\HttpFoundation\Response;

class JsonResponseAdapter extends AbstractResponseAdapter
{
	public function unauthorized(UnauthorizedResponse $response): Response
	{
		return $this->doAdapt($response, [
			'WWW-Authenticate' => implode(', ', $response->authSchemes())
		]);
	}

	public function methodNotAllowed(MethodNotAllowedResponse $response): Response
    {
        return $this->doAdapt($response, [
            'Allow' => $response->expectedMethod()
        ]);
    }

	protected function doAdapt(AbstractResponse $response, array $headers = []): Response
	{
		return \Response::json($response->toArray(), $response->code(), $headers);
	}
}