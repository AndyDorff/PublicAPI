<?php


namespace Modules\PublicAPI\Domain\Application;


use Modules\Core\Entities\AbstractIdentity;
use Modules\Core\Interfaces\ObjectInterface;
use Webmozart\Assert\Assert;
use function Modules\Core\strEqual;
use function Modules\PublicAPI\base64_rand;

final class ApplicationKey extends AbstractIdentity
{
    public function __construct(string $key)
    {
        $this->setKey($key);
    }

    public static function generate(string $seed = null): ApplicationKey
    {
        return new self(base64_rand(16, $seed));
    }

    protected function setKey(string $key)
    {
        Assert::length($key, 16, 'Application key string must consist of 16 characters');
        Assert::notFalse(base64_decode($key, true), 'Application key must be a valid base64 string');

        $this->attribute('key', $key);
    }

    public function equals(ObjectInterface $object): bool
    {
        return
            ($object instanceof self)
            && strEqual($this, $object)
         ;
    }

    public function __toString(): string
    {
        return $this->attribute('key');
    }
}
