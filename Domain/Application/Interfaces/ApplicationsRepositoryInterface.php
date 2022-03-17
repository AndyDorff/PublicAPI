<?php


namespace Modules\PublicAPI\Domain\Application\Interfaces;


use Illuminate\Support\Collection;
use Modules\PublicAPI\Domain\Application\Application;
use Modules\PublicAPI\Domain\Application\ApplicationKey;
use Modules\PublicAPI\Domain\Application\ApplicationSecret;
use Modules\PublicAPI\Domain\Application\ApplicationStatus;
use Modules\PublicAPI\Domain\Application\ApplicationVersion;

interface ApplicationsRepositoryInterface
{
    public function all(): Collection;
    public function getByCredentials(ApplicationKey $appKey, ApplicationSecret $appSecret): ?Application;

    public function find(ApplicationKey $key): ?Application;
    public function findMany(array $keys): Collection;

    public function newInstance(
        string $name,
        ApplicationVersion $version = null,
        ApplicationStatus $status = null
    ): Application;

    public function save(Application $application): void;
    public function delete(ApplicationKey $key): void;
}
