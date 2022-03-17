<?php

namespace spec\Modules\PublicAPI\Domain\Application;

use Modules\Core\Entities\AbstractValueObject;
use Modules\PublicAPI\Domain\Application\ApplicationVersion;
use PhpSpec\ObjectBehavior;

/**
 * Class ApplicationVersionSpec
 * @package spec\Modules\PublicAPI\Domain\Application
 * @mixin ApplicationVersion
 */
class ApplicationVersionSpec extends ObjectBehavior
{
    private $version;

    function let()
    {
        $this->version = 'v1';
        $this->beConstructedWith($this->version);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ApplicationVersion::class);
    }

    function it_is_a_ValueObject()
    {
        $this->shouldBeAnInstanceOf(AbstractValueObject::class);
    }

    function it_should_be_stringify()
    {
        $this->__toString()->shouldReturn($this->version);
    }

    function it_should_compares_with_another_ApplicationVersion()
    {

        $version1 = new ApplicationVersion($this->version);
        $version2 = new ApplicationVersion('2.0');

        $this->equals($version1)->shouldBe(true);
        $this->equals($version2)->shouldBe(false);
    }
}
