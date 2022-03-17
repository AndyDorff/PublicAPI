<?php


namespace Modules\PublicAPI\Services;


use Modules\PublicAPI\Domain\Application\Application;
use Modules\PublicAPI\Domain\Application\ApplicationKey;
use Modules\PublicAPI\Domain\Application\ApplicationStatus;
use Modules\PublicAPI\Domain\Application\ApplicationVersion;
use Modules\PublicAPI\Domain\Application\Interfaces\ApplicationsRepositoryInterface;
use Modules\PublicAPI\Domain\PersonalToken;
use Modules\PublicAPI\Dto\ApplicationDto;
use Modules\PublicAPI\Entities\AuthenticatableApplication;
use Tymon\JWTAuth\JWT;

final class ApplicationService
{
    /**
     * @var JWT
     */
    private $jwt;
    /**
     * @var ApplicationsRepositoryInterface
     */
    private $applications;

    private $passthru = [
        'find', 'findMany', 'all', 'getByCredentials'
    ];

    public function __construct(
        ApplicationsRepositoryInterface $applications
    )
    {
        $this->jwt = app('tymon.jwt');
        $this->applications = $applications;
    }

    public function generateJWTPersonalToken(Application $application, int $expiredAt = PersonalToken::EXPIRED_NEVER): PersonalToken
    {
        $isNeverExpired = ($expiredAt === PersonalToken::EXPIRED_NEVER);
        //for never expired tokens set expired date as date after 100 years
        $jwtSubject = (new AuthenticatableApplication($application))
            ->claims(['exp' => $isNeverExpired ? new \DateInterval('P100Y') : $expiredAt]);

        $jwtToken = $this->jwt->fromSubject($jwtSubject);
        $jwtProvider = $this->jwt->manager()->getJWTProvider();

        return PersonalToken::fromJWT($jwtToken, $jwtProvider, $expiredAt, false);
    }

    public function createApplication(ApplicationDto $applicationDto): Application
    {
        $application = $this->makeApplication($applicationDto);
        $this->saveApplication($application);

        return $application;
    }

    public function makeApplication(ApplicationDto $applicationDto): Application
    {
        return $this->applications->newInstance(
            $applicationDto->name,
            new ApplicationVersion($applicationDto->version),
            ApplicationStatus::fromString($applicationDto->status(), $applicationDto->statusDate())
        );
    }

    public function saveApplication(Application $application): void
    {
        $this->applications->save($application);
    }

    /**
     * @param ApplicationKey|string $appKey
     * @return Application
     */
    public function findApplicationOrFail($appKey): Application
    {
        if(!($appKey instanceof ApplicationKey)){
            $appKey = new ApplicationKey($appKey);
        }

        $application = $this->applications->find($appKey);
        if(!$application){
            throw new \Exception('Public api application with key "'.$appKey.'" not found');
        }

        return $application;
    }

    public function __call($name, $arguments)
    {
        if(
            method_exists($this->applications, $name)
            && in_array($name, $this->passthru)
        ){
            return call_user_func_array([$this->applications, $name], $arguments);
        }
    }
}