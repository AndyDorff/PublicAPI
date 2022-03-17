<?php


namespace Modules\PublicAPI\Tests\Unit;


use Modules\Core\Tests\Unit\Traits\TestConnection;
use Modules\PublicAPI\Domain\Application\ApplicationVersion;
use Modules\PublicAPI\Domain\Application\Repositories\EloquentApplicationsRepository;
use Modules\PublicAPI\Services\ApplicationService;
use Tests\TestCase;

class ApplicationServiceTest extends TestCase
{
    use TestConnection;

    private $application;
    private $repository;
    /**
     * @var ApplicationService
     */
    private $service;

    protected function connectionsToTransact()
    {
        return $this->initConnection();
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->repository = new EloquentApplicationsRepository($this->connection);
        $this->service = new ApplicationService($this->repository);
        $this->application = $this->repository->newInstance(
            'Some API application',
            new ApplicationVersion('1.0')
        );
    }

    /**
     * @dataProvider providePersonalTokenExpiredDates
     */
    public function test_it_should_generate_jwt_personal_token(int $expiredAt = null)
    {
        $personalToken = $this->service->generateJWTPersonalToken($this->application, $expiredAt);

        $this->assertEquals($expiredAt, $personalToken->expiredAt());
    }


    public function providePersonalTokenExpiredDates(): array
    {
        return [
            [time() + 3600],
            [null]
        ];
    }

    protected function afterInitConnection(): void
    {
        // TODO: Implement afterInitConnection() method.
    }
}