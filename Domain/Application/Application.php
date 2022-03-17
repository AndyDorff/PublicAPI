<?php


namespace Modules\PublicAPI\Domain\Application;


use Modules\Core\Entities\AbstractEntity;
use Modules\Core\Entities\AbstractIdentity;
use Modules\PublicAPI\Domain\PersonalToken;

class Application extends AbstractEntity
{
    public function __construct(
        string $name,
        ApplicationKey $key,
        ApplicationSecret $secret,
        ApplicationVersion $version = null,
        ApplicationStatus $status = null
    ) {
        parent::__construct($key);

        $this->setName($name);
        $this->setSecret($secret);
        $this->setVersion($version ?? new ApplicationVersion('v1'));
        $this->setStatus($status ?? ApplicationStatus::active());
    }

    public static function getAvailableStatuses(): array
    {
        $active = ApplicationStatus::active();
        $deleted = ApplicationStatus::deleted();

        return [
            (string)$active => $active,
            (string)$deleted => $deleted
        ];
    }

    protected function setName(string $name): void
    {
        $this->state('name', $name);
    }

    protected function setSecret(ApplicationSecret $secret): void
    {
        $this->state('secret', $secret);
    }

    protected function setVersion(ApplicationVersion $version): void
    {
        $this->state('version', $version);
    }

    protected function setId(AbstractIdentity $id): void
    {
        $this->state('key', $id);
    }

    protected function setStatus(ApplicationStatus $status): void
    {
        $this->state('status', $status);
    }

    public function setPersonalTokens(array $tokens): void
    {
        $tokens = array_reduce($tokens, function($result, PersonalToken $token){
            $result[$token->hashCode()] = $this->validateToken($token);
            return $result;
        }, []);
        $this->state('personalTokens', $tokens);
    }

    public function addPersonalToken(PersonalToken $token): void
    {
        $tokens = $this->personalTokens(true);
        $tokens[$token->hashCode()] = $this->validateToken($token);

        $this->state('personalTokens', $tokens);
    }

    private function validateToken(PersonalToken $token): PersonalToken
    {
        if($this->isPersonalTokenExist($token)){
            throw new \Exception('Given token "'.$token->hashCode().'" has already presented');
        }

        return $token;
    }

    public function isPersonalTokenExist(PersonalToken $token): bool
    {
        return isset($this->personalTokens(true)[$token->hashCode()]);
    }

    public function removePersonalToken(PersonalToken $token): void
    {
        if(!$this->isPersonalTokenExist($token)){
            throw new \Exception('Given token "'.$token->hashCode().'" has not presented');
        }

        $tokens = $this->personalTokens(true);
        unset($tokens[$token->hashCode()]);

        $this->state('personalTokens', $tokens);
    }

    /**
     * @return PersonalToken[]
     */
    public function personalTokens($preserveKeys = false): array
    {
        $tokens = $this->state('personalTokens') ?? [];

        return ($preserveKeys ? $tokens : array_values($tokens));
    }

    public function id(): AbstractIdentity
    {
        return $this->key();
    }

    public function key(): ApplicationKey
    {
        return $this->state('key');
    }

    public function __toString(): string
    {
        return $this->state('name');
    }

    public function rename(string $name): void
    {
        if($this->name() !== $name){
            $this->setName($name);
        }
    }

    public function name(): string
    {
        return $this->state('name');
    }

    public function switchToVersion(ApplicationVersion $version): void
    {
        if(!$this->version()->equals($version)){
            $this->setVersion($version);
        }
    }

    public function version(): ApplicationVersion
    {
        return $this->state('version');
    }

    public function secret(): ApplicationSecret
    {
        return $this->state('secret');
    }

    public function activate(): void
    {
        $active = ApplicationStatus::active();
        if(!$this->status()->is($active)){
            $this->setStatus($this->status()->and($active));
        }
    }

    public function deactivate(): void
    {
        $active = ApplicationStatus::active();
        if($this->status()->is($active)){
            $this->setStatus($this->status()->not($active));
        }
    }

    public function delete(): void
    {
        $deleted = ApplicationStatus::deleted();
        if(!$this->status()->is($deleted)){
            $this->setStatus($this->status()->and($deleted));
        }
    }

    public function restore(): void
    {
        $deleted = ApplicationStatus::deleted();
        if($this->status()->is($deleted)){
            $this->setStatus($this->status()->not($deleted));
        }
    }

    public function isActive(): bool
    {
        return $this->status()->is(ApplicationStatus::active());
    }

    public function isDeleted(): bool
    {
        return $this->status()->is(ApplicationStatus::deleted());
    }

    public function status(): ApplicationStatus
    {
        return $this->state('status');
    }
}
