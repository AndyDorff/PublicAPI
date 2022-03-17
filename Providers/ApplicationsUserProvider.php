<?php


namespace Modules\PublicAPI\Providers;


use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Modules\Core\Interfaces\ObjectInterface;
use Modules\PublicAPI\Domain\Application\ApplicationKey;
use Modules\PublicAPI\Domain\Application\ApplicationSecret;
use Modules\PublicAPI\Domain\Application\Interfaces\ApplicationsRepositoryInterface;
use Modules\PublicAPI\Entities\AuthenticatableApplication;
use function Modules\Core\strEqual;

class ApplicationsUserProvider implements UserProvider
{
    /**
     * @var ApplicationsRepositoryInterface
     */
    private $applications;

    public function __construct(ApplicationsRepositoryInterface $applications)
    {
        $this->applications = $applications;
    }

    public function retrieveById($identifier)
    {
        $appKey = $this->normalizeIdentifier($identifier);
        $application = $this->applications->find($appKey);

        return ($application ? new AuthenticatableApplication($application) : null);
    }

    public function retrieveByToken($identifier, $token)
    {
        return null;
    }

    public function updateRememberToken(Authenticatable $user, $token)
    {
    }

    public function retrieveByCredentials(array $credentials)
    {
        /**
         * @var ApplicationKey $appKey
         * @var ApplicationSecret $appSecret
         */
        list($appKey, $appSecret) = $this->normalizeCredentials($credentials);
        $application = $this->applications->getByCredentials($appKey, $appSecret);

        return ($application ? new AuthenticatableApplication($application) : null);
    }

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        list($appKey, $appSecret) = $this->normalizeCredentials($credentials);

        return (
            strEqual($appKey, $user->getAuthIdentifier())
            && strEqual($appSecret, $user->getAuthPassword())
        );
    }

    /**
     * @param array $credentials
     * @return ObjectInterface[]
     */
    private function normalizeCredentials(array $credentials)
    {
        list($identifier, $password) = $credentials;

        return [
            $this->normalizeIdentifier($identifier),
            $this->normalizePassword($password)
        ];
    }

    private function normalizeIdentifier($identifier): ApplicationKey
    {
        if($identifier instanceof ApplicationKey){
            return $identifier;
        }
        else{
            return new ApplicationKey(strval($identifier));
        }
    }

    private function normalizePassword($password): ApplicationSecret
    {
        if($password instanceof ApplicationSecret){
            return $password;
        }
        else{
            return new ApplicationSecret(strval($password));
        }
    }
}
