<?php


namespace Modules\PublicAPI\Dto;


use Modules\Core\Entities\AbstractDataTransferObject;
use Modules\PublicAPI\Domain\Application\ApplicationStatus;

class ApplicationDto extends AbstractDataTransferObject
{
    public $name;
    public $version;
    protected $status;
    protected $statusDate;

    /**
     * @see ApplicationStatus for availabel statuses
     */
    public static function forCreating(
        string $name,
        string $version,
        string $status = null,
        \DateTime $statusDate = null
    ){
        $dto = new static(compact('name', 'version'));
        $dto->setStatus($status ?? 'active', $statusDate);

        return $dto;
    }

    /**
     * @see ApplicationStatus for availabel statuses
     */
    public function setStatus(string $status, \DateTime $statusDate = null): void
    {
        $this->status = $status;
        $this->statusDate = $statusDate;
    }

    public function status(): string
    {
        return $this->status;
    }

    public function statusDate(): ?\DateTime
    {
        return $this->statusDate;
    }
}