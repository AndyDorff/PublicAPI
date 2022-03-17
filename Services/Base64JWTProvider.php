<?php


namespace Modules\PublicAPI\Services;


use Tymon\JWTAuth\Contracts\Providers\JWT;

class Base64JWTProvider implements JWT
{
    public function encode(array $payload)
    {
    }

    public function decode($token): array
    {
        return json_decode(base64_decode(explode('.', $token)[1]), true);
    }
}