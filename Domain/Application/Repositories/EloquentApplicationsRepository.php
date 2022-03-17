<?php


namespace Modules\PublicAPI\Domain\Application\Repositories;


use Illuminate\Database\ConnectionInterface as Connection;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Modules\Core\Entities\SpecialTypes\Map;
use Modules\PublicAPI\Domain\Application\Application;
use Modules\PublicAPI\Domain\Application\ApplicationKey;
use Modules\PublicAPI\Domain\Application\ApplicationSecret;
use Modules\PublicAPI\Domain\Application\ApplicationStatus;
use Modules\PublicAPI\Domain\Application\ApplicationVersion;
use Modules\PublicAPI\Domain\PersonalToken;

class EloquentApplicationsRepository extends AbstractApplicationsRepository
{
    const TABLE_NAME = 'pa_applications';
    const PERSONAL_TOKENS_TABLE_NAME = 'pa_personal_tokens';

    /**
     * @var Connection
     */
    private $connection;
    private $entityMap;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->entityMap = new Map();
    }

    public function connection(): Connection
    {
        return $this->connection;
    }

    public function all(): Collection
    {
        return $this->query()->get();
    }

    public function getByCredentials(ApplicationKey $appKey, ApplicationSecret $appSecret): ?Application
    {
        $application = $this->find($appKey);
        if($application && $application->secret()->equals($appSecret)){
            return $application;
        }

        return null;
    }

    public function findMany(array $keys): Collection
    {
        $arrKeys = array_map(function(ApplicationKey $key){
            return (string)$key;
        }, $keys);

        $result = $this->get($this->query('pa')->whereIn('pa.key', $arrKeys));

        return $result->values();
    }

    public function find(ApplicationKey $key): ?Application
    {
        if($this->entityMap->has($key)){
            $application = $this->entityMap->get($key);
        }
        else{
            $application = $this->doFind($key);
            $this->entityMap = $this->entityMap->set($key, $application);
        }

        return $application;
    }

    private function doFind(string $key): ?Application
    {
        return $this->get($this->query('pa')->where('pa.key', $key))->first();
    }


    private function get(Builder $query): Collection
    {
        $result = $query
            ->leftJoin(self::PERSONAL_TOKENS_TABLE_NAME . ' as pa_pt', function (JoinClause $query) {
                $query
                    ->on('pa_pt.key', '=', 'pa.key')
                    ->where('revoked', false);
            })
            ->get(['pa.*', 'pa_pt.hash', 'pa_pt.expired_at'])
            ->reduce(function (Collection $result, $appData) {
                $appData = (array)$appData;
                $key = $appData['key'];
                $value = $result->get($key, $appData + ['personal_tokens' => []]);
                if ($appData['hash']) {
                    $value['personal_tokens'][] = [
                        'token' => $appData['hash'],
                        'expired_at' => $appData['expired_at']
                    ];
                }
                return $result->put($key, $value);
            }, collect([]))
            ->map(function ($appData) {
                $appKey = new ApplicationKey($appData['key']);
                return ($this->entityMap->get($appKey) ?? $this->reverseMap($appData));
            });

        return $result;
    }

    public function save(Application $application): void
    {
        if($this->find($application->key())){
            if($application->getState()->isChanged()){
                $this->update($application);
            }
        }
        else{
            $this->insert($application);
        }

        $this->entityMap = $this->entityMap->set($application->key(), $application);
    }

    protected function insert(Application $application): void
    {
        [$applicationAttributes, $personalTokensAttributes] = $this->mapApplication($application);

        $this->query()->insert($applicationAttributes);
        $this->personaTokensQuery()->insert($personalTokensAttributes);
    }

    protected function update(Application $application): void
    {
        [$applicationAttributes, $personalTokensAttributes] = $this->mapApplication($application);
        $where = ['key' => $applicationAttributes['key'], 'secret' => $applicationAttributes['secret']];
        unset($applicationAttributes['key'], $applicationAttributes['secret']);

        $this->query()
            ->where($where)
            ->update($applicationAttributes);

        if($application->getState()->isChanged('personalTokens')){
            $this->syncPersonalTokens($application, $personalTokensAttributes);
        }
    }

    private function syncPersonalTokens(Application $application, array $currentTokensAttributes): void
    {
        $originTokens = $application->getOrigin()->get('personalTokens');

        $new = array_diff_key($currentTokensAttributes, $originTokens);
        $old = array_diff_key($originTokens, $currentTokensAttributes);

        if(!empty($new)){
            $this->personaTokensQuery()->insert($new);
        }

        if(!empty($old)){
            $this->personaTokensQuery()
                ->where('key', $application->key()->__toString())
                ->whereIn('hash', array_keys($old))
                ->update(['revoked' => true]);
        }

    }

    private function mapApplication(Application $application): array
    {
        $applicationAttributes = array_filter([
            'name' => $application->name(),
            'key' => strval($application->key()),
            'secret' => strval($application->secret()),
            'version' => strval($application->version()),
            'status' => strval($application->status()),
            'status_code' => $application->status()->code(),
            'status_date' => $application->status()->date()->format('Y-m-d H:i:s'),
            'updated_at' => new Carbon(),
            'created_at' => $this->find($application->key()) ? null : new Carbon()
        ], function($value) {
            return !is_null($value);
        });

        $personalTokensAttributes = array_map(function(PersonalToken $token) use ($application){
            return [
                'key' => $application->key()->__toString(),
                'hash' => $token->hashCode(),
                'expired_at' => $token->expiredAt()
            ];
        }, $application->personalTokens(true));

        return [$applicationAttributes, $personalTokensAttributes];
    }

    private function reverseMap(array $attributes): Application
    {
        $application = new Application(
            $attributes['name'],
            new ApplicationKey($attributes['key']),
            new ApplicationSecret($attributes['secret']),
            new ApplicationVersion($attributes['version']),
            $this->createApplicationStatus($attributes['status'], $attributes['status_date'])
        );

        $application->setPersonalTokens(array_map(function($personalTokenData){
            return new PersonalToken($personalTokenData['token'], $personalTokenData['expired_at']);
        }, $attributes['personal_tokens']));

        return $application;
    }

    private function createApplicationStatus(string $status, string $date)
    {
        $date = \DateTime::createFromFormat('Y-m-d H:i:s', $date);

        return ApplicationStatus::fromString($status, $date);
    }

    private function query(string $alias = null): Builder
    {
        $table = self::TABLE_NAME.($alias ? ' as '.$alias : '');
        $statusCode = ApplicationStatus::deleted()->code();

        return $this->connection->table($table)->whereRaw(
            '(status_code & '.$statusCode.') != '.$statusCode
        );
    }

    private function personaTokensQuery(): Builder
    {
        return $this->connection->table(self::PERSONAL_TOKENS_TABLE_NAME);
    }

    public function delete(ApplicationKey $key): void
    {
        if($application = $this->find($key)){
            $application->delete();
            $this->save($application);

            $this->entityMap = $this->entityMap->unset($key);
        }
    }
}
