<?php


namespace Modules\PublicAPI\Http\ResponseStatuses;


abstract class AbstractResponseStatus
{
	/**
	 * @var string
	 */
	private $code;
	/**
	 * @var string
	 */
	private $message;
	/**
	 * @var array
	 */
	private $data;

	public function __construct(string $code, string $message, array $data = [])
	{
		$this->setCode($code);
		$this->setMessage($message);
		$this->setData($data);
	}

	abstract public function type(): string;

	/**
	 * @return string
	 */
	public function code(): string
	{
		return $this->code;
	}

	/**
	 * @param string $code
	 */
	protected function setCode(string $code): void
	{
		$this->code = $code;
	}

	/**
	 * @return string
	 */
	public function message(): string
	{
		return $this->message;
	}

	/**
	 * @param string $message
	 */
	protected function setMessage(string $message): void
	{
		$this->message = $message;
	}

	/**
	 * @return array
	 */
	public function data(): array
	{
		return $this->data;
	}

	/**
	 * @param array $data
	 */
	protected function setData(array $data): void
	{
		$this->data = $data;
	}
}