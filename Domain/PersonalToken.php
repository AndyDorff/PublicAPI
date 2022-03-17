<?php


namespace Modules\PublicAPI\Domain;


use Modules\Core\Entities\AbstractValueObject;
use Modules\Core\Interfaces\ObjectInterface;
use Tymon\JWTAuth\Contracts\Providers\JWT;

final class PersonalToken extends AbstractValueObject
{
    const EXPIRED_NEVER = null;

    private $hash;

    public function __construct(string $token, int $expiredAt = self::EXPIRED_NEVER)
    {
        $this->initAttributes([
            'token' => $token,
            'expiredAt' => $expiredAt
        ]);
        $this->hash = substr(md5($token), 16);
    }

    public static function fromJWT(
        string $jwtToken,
        JWT $jwtProvider,
        int $expiredAt = self::EXPIRED_NEVER,
        bool $extractExpiredDateFromToken = true
    ): self {
        $payload = $jwtProvider->decode($jwtToken);
        $expiredAt = $extractExpiredDateFromToken ? ($payload['exp'] ?? $expiredAt) : $expiredAt;
        $token = new static ($jwtToken, $expiredAt);
        $token->hash = $payload['jti'] ?? $token->hash;

        return $token;
    }

    public function equals(ObjectInterface $object): bool
    {
        return ($object instanceof self && $object->token() === $this->token());
    }

    private function token(): string
    {
        return $this->attribute('token');
    }

    public function isExpired(): bool
    {
        $expiredAt = $this->expiredAt();

        return (
            $expiredAt !== self::EXPIRED_NEVER
            && $expiredAt <= time()
        );
    }

    public function expiredAt(): ?int
    {
        return $this->attribute('expiredAt');
    }

    public function __toString(): string
    {
        return $this->token();
    }

    public function hashCode(): string
    {
        return $this->hash;
    }
}