<?php

namespace Modules\PublicAPI\Tests\Unit;

use Modules\Core\Tests\Unit\Traits\TestConnection;
use Modules\PublicAPI\Domain\Application\Application;
use Modules\PublicAPI\Domain\Application\ApplicationKey;
use Modules\PublicAPI\Domain\Application\ApplicationSecret;
use Modules\PublicAPI\Domain\Application\ApplicationStatus;
use Modules\PublicAPI\Domain\Application\ApplicationVersion;
use Modules\PublicAPI\Domain\Application\Repositories\EloquentApplicationsRepository;
use Modules\PublicAPI\Domain\PersonalToken;
use Tests\TestCase;

class EloquentApplicationsRepositoryTest extends TestCase
{
    use TestConnection;

    private $application;
    private $repository;

    protected function connectionsToTransact()
    {
        return $this->initConnection();
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->repository = new EloquentApplicationsRepository($this->connection);
        $this->application = $this->repository->newInstance(
            'Some API application',
            new ApplicationVersion('1.0')
        );
    }

    public function test_it_should_be_constructed_with_connection()
    {
        new EloquentApplicationsRepository($this->connection);

        $this->addToAssertionCount(1);
    }

    public function test_it_should_return_connection()
    {
        $connection = $this->repository->connection();

        $this->assertSame($this->connection, $connection);
    }

    public function test_it_should_creates_new_instance_of_Application()
    {
        $application = $this->repository->newInstance(
            'Some App name',
            $version = new ApplicationVersion('1.0'),
            $status = ApplicationStatus::active()
        );

        $this->assertInstanceOf(Application::class, $application);
        $this->assertSame($application->name(), 'Some App name');
        $this->assertTrue($application->version()->equals($version));
        $this->assertTrue($application->status()->equals($status));
    }

    public function test_it_should_save_new_Application()
    {
        $this->repository->save($this->application);
        $application = $this->repository->find($this->application->key());

        $this->assertSame($this->application, $application);
    }

    public function test_it_should_save_new_Application_with_personal_tokens()
    {
        $this->application->setPersonalTokens($personalTokens = [
            new PersonalToken('token_1'),
            new PersonalToken('token_2'),
            new PersonalToken('token_3', time() + 3600)
        ]);

        $this->repository->save($this->application);

        $tokens = $this->repository->connection()->table(EloquentApplicationsRepository::PERSONAL_TOKENS_TABLE_NAME)
            ->where(['key' => $this->application->key()->__toString()])
            ->get();

        $this->assertCount(3, $tokens);
        $tokens->each(function($token, $i) use ($personalTokens){
            $this->assertSame($personalTokens[$i]->hashCode(), $token->hash);
            $this->assertSame($personalTokens[$i]->expiredAt(), is_null($token->expired_at) ? null : intval($token->expired_at));
        });
    }

    public function test_it_should_find_Application()
    {
        $version = new ApplicationVersion('1.0');
        $application2 = $this->repository->newInstance('Some Another App Name', $version);
        $application3 = $this->repository->newInstance('Some Another App Name 2', $version);

        $this->repository->save($this->application);
        $this->repository->save($application2);
        $this->repository->save($application3);

        $foundApplication2 = $this->repository->find($application2->key());
        $foundApplication3 = $this->repository->find($application3->key());

        $this->assertSame($application2, $foundApplication2);
        $this->assertSame($application3, $foundApplication3);
    }

    public function test_it_should_save_changed_Application()
    {
        $this->repository->save($this->application);
        $this->application->rename('Application better name');

        $this->repository->save($this->application);
        $application = $this->repository->find($this->application->key());

        $this->assertSame($this->application->name(), $application->name());
        $this->assertSame($this->application, $application);
    }

    public function test_it_should_sync_Application_personal_tokens_if_token_added()
    {
        $this->application->setPersonalTokens([
            new PersonalToken('token_1'),
            new PersonalToken('token_2'),
        ]);
        $this->repository->save($this->application);

        $this->application->addPersonalToken($personalToken = new PersonalToken('token_3', time() + 3600));
        $this->repository->save($this->application);

        $tokens = $this->repository->connection()->table(EloquentApplicationsRepository::PERSONAL_TOKENS_TABLE_NAME)
            ->where(['key' => $this->application->key()->__toString()])
            ->get();

        $this->assertCount(3, $tokens);
        $this->assertSame($personalToken->hashCode(), $tokens[2]->hash);
        $this->assertSame($personalToken->expiredAt(), is_null($tokens[2]->expired_at) ? null : intval($tokens[2]->expired_at));
    }

    public function test_it_should_sync_Application_personal_tokens_if_token_removed()
    {
        $this->application->setPersonalTokens($personalTokens = [
            new PersonalToken('token_1'),
            new PersonalToken('token_2'),
            new PersonalToken('token_3', time() + 3600)
        ]);
        $this->repository->save($this->application);

        $this->application->removePersonalToken($personalTokens[1]);
        $this->application->removePersonalToken($personalTokens[0]);
        $this->repository->save($this->application);

        $tokens = $this->repository->connection()->table(EloquentApplicationsRepository::PERSONAL_TOKENS_TABLE_NAME)
            ->where(['key' => $this->application->key()->__toString()])
            ->get();

        $this->assertCount(3, $tokens);
        $this->assertTrue($tokens[0]->revoked === '1');
        $this->assertTrue($tokens[1]->revoked === '1');
        $this->assertFalse($tokens[2]->revoked === '2');
    }

    public function test_it_should_be_not_saved_if_Application_not_changed()
    {
        $repository = $this->getMockBuilder(EloquentApplicationsRepository::class)
            ->setConstructorArgs([$this->connection])
            ->setMethods(['update'])
            ->getMock();

        $repository->save($this->application);

        $repository->expects($this->never())
            ->method('update');

        $repository->save($this->application);
    }

    public function test_it_should_delete_an_Application()
    {
        $this->repository->save($this->application);
        $this->repository->delete($this->application->key());

        $this->assertTrue($this->application->isDeleted());
        $this->assertNull($this->repository->find($this->application->key()));
    }

    public function test_it_should_find_many_Applications()
    {
        $app1 = $this->repository->newInstance('App 1');
        $app2 = $this->repository->newInstance('App 2');
        $app3 = $this->repository->newInstance('App 3');
        $app3->setPersonalTokens([
            new PersonalToken('token_1'),
            new PersonalToken('token_2'),
            new PersonalToken('token_3', time() + 3600)
        ]);

        $this->repository->save($app1);
        $this->repository->save($app2);
        $this->repository->save($app3);

        $applications = $this->repository->findMany([ $app1->key(), $app2->key(), $app3->key() ])
            ->keyBy(function(Application $app){
            return (string)$app->key();
        });

        $this->assertCount(3, $applications);
        $this->assertSame($app1, $applications->get((string)$app1->key()));
        $this->assertSame($app2, $applications->get((string)$app2->key()));
        $this->assertSame($app3, $applications->get((string)$app3->key()));
    }

    public function test_it_should_get_application_by_credentials()
    {
        $this->repository->save($this->application);

        $app = $this->repository->getByCredentials($this->application->key(), $this->application->secret());
        $this->assertSame($this->application, $app);

        $app = $this->repository->getByCredentials($this->application->key(), ApplicationSecret::generate());
        $this->assertNotSame($this->application, $app);

        $app = $this->repository->getByCredentials(ApplicationKey::generate(), $this->application->secret());
        $this->assertNotSame($this->application, $app);
    }

	protected function afterInitConnection(): void
	{
		\Artisan::call('module:migrate --database=sqlite_testing PublicAPI');
	}
}
