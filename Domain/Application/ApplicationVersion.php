<?php


namespace Modules\PublicAPI\Domain\Application;


use Modules\Core\Entities\AbstractValueObject;
use Modules\Core\Interfaces\ObjectInterface;
use function Modules\Core\strEqual;

final class ApplicationVersion extends AbstractValueObject
{
    public function __construct(string $versionNumber)
    {
        $this->attribute('versionNumber', $versionNumber);
    }

    public function equals(ObjectInterface $object): bool
    {
        return ($object instanceof self)
            && strEqual($this, $object)
        ;
    }

    public function __toString(): string
    {
        return $this->attribute('versionNumber');
    }
}
