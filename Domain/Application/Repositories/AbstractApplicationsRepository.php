<?php


namespace Modules\PublicAPI\Domain\Application\Repositories;


use Modules\PublicAPI\Domain\Application\Application;
use Modules\PublicAPI\Domain\Application\ApplicationKey;
use Modules\PublicAPI\Domain\Application\ApplicationSecret;
use Modules\PublicAPI\Domain\Application\ApplicationStatus;
use Modules\PublicAPI\Domain\Application\ApplicationVersion;
use Modules\PublicAPI\Domain\Application\Interfaces\ApplicationsRepositoryInterface;

abstract class AbstractApplicationsRepository implements ApplicationsRepositoryInterface
{
    public function newInstance(
        string $name,
        ApplicationVersion $version = null,
        ApplicationStatus $status = null
    ): Application {
        return new Application($name, ApplicationKey::generate(), ApplicationSecret::generate(), $version, $status);
    }
}
