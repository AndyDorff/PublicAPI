<?php


namespace Modules\PublicAPI\Entities;


use Illuminate\Contracts\Auth\Authenticatable;
use Modules\PublicAPI\Domain\Application\Application;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Tymon\JWTAuth\Support\CustomClaims;

class AuthenticatableApplication implements Authenticatable, JWTSubject
{
    use CustomClaims;
    /**
     * @var Application
     */
    private $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function getApplication(): Application
    {
        return $this->application;
    }

    public function getAuthIdentifierName()
    {
        return $this->application->name();
    }

    public function getAuthIdentifier()
    {
        return (string)$this->application->key();
    }

    public function getAuthPassword()
    {
        return (string)$this->application->secret();
    }

    public function getRememberToken()
    {
        return '';
    }

    public function setRememberToken($value)
    {
    }

    public function getRememberTokenName()
    {
        return '';
    }

    public function getJWTIdentifier()
    {
        return $this->getAuthIdentifier();
    }

    public function getJWTCustomClaims()
    {
        return $this->getCustomClaims();
    }
}
