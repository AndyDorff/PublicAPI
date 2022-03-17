<?php

namespace spec\Modules\PublicAPI\Domain\Application;

use Modules\Core\Entities\AbstractEntity;
use Modules\PublicAPI\Domain\Application\Application;
use Modules\PublicAPI\Domain\Application\ApplicationKey;
use Modules\PublicAPI\Domain\Application\ApplicationSecret;
use Modules\PublicAPI\Domain\Application\ApplicationStatus;
use Modules\PublicAPI\Domain\Application\ApplicationVersion;
use Modules\PublicAPI\Domain\PersonalToken;
use PhpSpec\ObjectBehavior;

/**
 * Class ApplicationSpec
 * @package spec\Modules\PublicAPI\Domain\Application
 * @mixin Application
 */
class ApplicationSpec extends ObjectBehavior
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var ApplicationKey
     */
    private $appKey;
    /**
     * @var ApplicationSecret
     */
    private $appSecret;
    /**
     * @var ApplicationVersion
     */
    private $appVersion;
    /**
     * @var ApplicationStatus
     */
    private $appStatus;
    /**
     * @var PersonalToken[]
     */
    private $personalTokens = [];

    function let()
    {
        $this->name = 'Api Application name';
        $this->appKey = ApplicationKey::generate();
        $this->appSecret = ApplicationSecret::generate();
        $this->appVersion = new ApplicationVersion('1.0');
        $this->appStatus = ApplicationStatus::active();
        $this->personalTokens = [
            new PersonalToken('token1'),
            new PersonalToken('token2')
        ];

        $this->beConstructedWith($this->name, $this->appKey, $this->appSecret, $this->appVersion, $this->appStatus);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Application::class);
    }

    function it_is_an_Entity()
    {
        $this->shouldBeAnInstanceOf(AbstractEntity::class);
    }

    function it_should_has_name()
    {
        $this->name()->shouldBe($this->name);
    }

    function it_should_change_name()
    {
        $name = 'Some another name';
        $this->rename($name);

        $this->name()->shouldBe($name);
    }

    function it_should_has_key()
    {
        $this->key()->equals($this->appKey)->shouldBe(true);
    }

    function it_should_has_secret()
    {
        $this->secret()->equals($this->appSecret)->shouldBe(true);
    }

    function it_should_has_version()
    {
        $this->version()->equals($this->appVersion)->shouldBe(true);
    }

    function it_should_switch_to_another_version()
    {
        $version = new ApplicationVersion('2.0');
        $this->switchToVersion($version);

        $this->version()->equals($this->appVersion)->shouldBe(false);
        $this->version()->equals($version)->shouldBe(true);
    }

    function it_should_has_status()
    {
        $this->status()->equals($this->appStatus)->shouldReturn(true);
    }

    function it_should_has_active_status_if_status_not_defined()
    {
        $this->beConstructedWith(
            $this->name,
            $this->appKey,
            $this->appSecret
        );
        $this->status()->equals(ApplicationStatus::active())->shouldBe(true);
    }

    function it_should_has_first_version_by_default()
    {
        $this->beConstructedWith(
            $this->name,
            $this->appKey,
            $this->appSecret
        );
        $this->version()->equals(new ApplicationVersion('v1'))->shouldReturn(true);
    }

    function it_should_be_activated()
    {
        $this->activate();
        $this->status()->is(ApplicationStatus::active())->shouldBe(true);
    }

    function it_should_be_deactivated()
    {
        $this->deactivate();
        $this->status()->is(ApplicationStatus::active())->shouldBe(false);
    }

    function it_should_be_deleted()
    {
        $this->delete();
        $this->status()->is(ApplicationStatus::deleted())->shouldBe(true);
    }

    function it_should_be_restored()
    {
        $this->delete();
        $this->restore();
        $this->status()->is(ApplicationStatus::deleted())->shouldBe(false);
    }

    function it_should_be_checked_if_active()
    {
        $this->isActive()->shouldBe(true);
        $this->deactivate();
        $this->isActive()->shouldBe(false);
    }

    function it_should_be_checked_if_deleted()
    {
        $this->isDeleted()->shouldBe(false);
        $this->delete();
        $this->isDeleted()->shouldBe(true);
    }

    function it_can_contains_personal_tokens()
    {
        $this->personalTokens()->shouldBe([]);
    }

    function it_can_set_multiple_personal_tokens()
    {
        $this->setPersonalTokens($this->personalTokens);
        $this->personalTokens()->shouldBe($this->personalTokens);
    }

    function it_should_add_personal_token()
    {
        $token = new PersonalToken('token3');

        $this->setPersonalTokens($this->personalTokens);
        $this->addPersonalToken($token);

        $this->personalTokens()->shouldHaveCount(3);
        $this->isPersonalTokenExist($token)->shouldReturn(true);
    }

    function it_should_remove_personal_token()
    {
        $token = $this->personalTokens[0];

        $this->setPersonalTokens($this->personalTokens);
        $this->removePersonalToken($token);

        $this->personalTokens()->shouldHaveCount(1);
        $this->isPersonalTokenExist($token)->shouldReturn(false);
    }
}
