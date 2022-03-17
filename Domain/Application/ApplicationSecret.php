<?php


namespace Modules\PublicAPI\Domain\Application;


use Modules\Core\Entities\AbstractValueObject;
use Modules\Core\Interfaces\ObjectInterface;
use Webmozart\Assert\Assert;
use function Modules\Core\strEqual;
use function Modules\PublicAPI\base64_rand;

final class ApplicationSecret extends AbstractValueObject
{
    public function __construct(string $secret)
    {
        $this->setSecret($secret);
    }

    public static function generate(string $seed = null): ApplicationSecret
    {
        return new self(base64_rand(32, $seed));
    }

    protected function setSecret(string $secret)
    {
        Assert::length($secret, 32, 'Application secret string must consist of 32 characters');
        Assert::notFalse(base64_decode($secret, true), 'Application secret must be a valid base64 string');

        $this->attribute('secret', $secret);
    }

    public function equals(ObjectInterface $object): bool
    {
        return
            ($object instanceof self)
            && strEqual($this, $object);
    }

    public function __toString(): string
    {
        return $this->attribute('secret');
    }
}
