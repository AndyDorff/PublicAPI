<?php


namespace Modules\PublicAPI\Factories\Response;


use Modules\PublicAPI\Factories\Response\Adapters\AbstractResponseAdapter;
use Modules\PublicAPI\Factories\Response\Base\ErrorResponseFactory;
use Modules\PublicAPI\Factories\Response\Base\FailResponseFactory;
use Modules\PublicAPI\Factories\Response\Base\InfoResponseFactory;
use Modules\PublicAPI\Factories\Response\Base\SuccessResponseFactory;
use Modules\PublicAPI\Http\Responses\AbstractResponse;
use Symfony\Component\HttpFoundation\Response;

final class ResponseFactory
{
	/**
	 * @var InfoResponseFactory
	 */
	private $info;
	/**
	 * @var SuccessResponseFactory
	 */
	private $success;
	/**
	 * @var ErrorResponseFactory
	 */
	private $error;
	/**
	 * @var FailResponseFactory
	 */
	private $fail;

	public function __construct()
	{
		$this->info = app(InfoResponseFactory::class);
		$this->success = app(SuccessResponseFactory::class);
		$this->error = app(ErrorResponseFactory::class);
		$this->fail = app(FailResponseFactory::class);
	}

	/**
	 * @return InfoResponseFactory
	 */
	public function info(): InfoResponseFactory
	{
		return $this->info;
	}

	/**
	 * @return SuccessResponseFactory
	 */
	public function success(): SuccessResponseFactory
	{
		return $this->success;
	}

	/**
	 * @return ErrorResponseFactory
	 */
	public function error(): ErrorResponseFactory
	{
		return $this->error;
	}

	/**
	 * @return FailResponseFactory
	 */
	public function fail(): FailResponseFactory
	{
		return $this->fail;
	}
}