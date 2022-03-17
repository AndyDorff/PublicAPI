<?php


namespace Modules\PublicAPI\Domain\Application;


use Modules\Core\Entities\AbstractValueObject;
use Modules\Core\Entities\SpecialTypes\Status\Status;
use Modules\Core\Entities\SpecialTypes\Status\StatusCode;
use Modules\Core\Interfaces\ObjectInterface;

final class ApplicationStatus extends AbstractValueObject
{
    const STATUS_CODE_ACTIVE = 1;
    const STATUS_CODE_DELETED = 2;

    private function __construct(Status $status)
    {
        $this->attribute('status', $status);
    }

    public static function fromString(string $status, \DateTime $dateTime = null): self
    {
        $appStatus = new ApplicationStatus(Status::emptyStatus($dateTime));
        $names = explode(',', $status);
        if($names){
            foreach($names as $statusName){
                switch($statusName){
                    case '':
                        break;
                    case 'active':
                        $appStatus = $appStatus->and(self::active($dateTime));
                        break;
                    case 'deleted':
                        $appStatus = $appStatus->and(self::deleted($dateTime));
                        break;
                    default:
                        throw new \DomainException('Status with name "'.$status.'" not available');
                }
            }
        }

        return $appStatus;
    }

    public static function active(\DateTime $dateTime = null): self
    {
        return new self(new Status(
            new StatusCode(self::STATUS_CODE_ACTIVE, 'active'),
            $dateTime
        ));
    }

    public static function deleted(\DateTime $dateTime = null): self
    {
        return new self(new Status(
            new StatusCode(self::STATUS_CODE_DELETED, 'deleted'),
            $dateTime
        ));
    }

    public function code(): int
    {
        return $this->status()->code();
    }

    public function date(): \DateTime
    {
        return $this->status()->date();
    }

    public function and(ApplicationStatus $status): ApplicationStatus
    {
        return new ApplicationStatus(
            $this->status()->and($status->status())
        );
    }

    public function not(ApplicationStatus $status)
    {
        return new ApplicationStatus(
            $this->status()->not($status->status())
        );
    }

    public function is(ApplicationStatus $status)
    {
        return $this->status()->is($status->status());
    }

    public function equals(ObjectInterface $object): bool
    {
        return (
            ($object instanceof self)
            && $this->status()->equals($object->status())
        );
    }

    private function status(): Status
    {
        return $this->attribute('status');
    }

    public function __toString(): string
    {
        return $this->status()->__toString();
    }
}
