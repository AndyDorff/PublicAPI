<?php


namespace Modules\PublicAPI\Http\Responses;


use Illuminate\Contracts\Support\Arrayable;
use Modules\PublicAPI\Http\ResponseStatuses\AbstractResponseStatus;

abstract class AbstractResponse implements Arrayable
{
	/**
	 * @var int
	 */
	private $code;
	/**
	 * @var AbstractResponseStatus
	 */
	private $status;

	public function __construct(int $code, AbstractResponseStatus $status)
	{
		$this->setCode($code);
		$this->setStatus($status);
	}

	protected function setCode(int $code): void
	{
		$this->code = $code;
	}

	protected function setStatus(AbstractResponseStatus $status): void
	{
		$this->status = $status;
	}

	public function status(): AbstractResponseStatus
	{
		return $this->status;
	}

	public function code(): int
	{
		return $this->code;
	}

	/**
	 * @return string
	 */
	public function message(): string
	{
		return $this->status()->message();
	}

	/**
	 * @return array
	 */
	public function data(): array
	{
		return $this->status->data();
	}

	public function toJson(int $options = 0, int $depth = 512): string
	{
		return json_encode($this->toArray(), $options, $depth);
	}

	public function toArray(): array
	{
		return [
			'code' => $this->code(),
			'status' => $this->status()->type(),
			'status_code' => $this->status()->code(),
			'message' => $this->message(),
			'data' => $this->data()
		];
	}
}