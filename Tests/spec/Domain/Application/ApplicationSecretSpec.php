<?php

namespace spec\Modules\PublicAPI\Domain\Application;

use InvalidArgumentException;
use Modules\Core\Entities\AbstractValueObject;
use Modules\PublicAPI\Domain\Application\ApplicationSecret;
use PhpSpec\ObjectBehavior;
use function Modules\PublicAPI\base64_rand;

/**
 * Class ApplicationSecretSpec
 * @package spec\Modules\PublicAPI\Domain\Application
 * @mixin ApplicationSecret
 */
class ApplicationSecretSpec extends ObjectBehavior
{
    private $secret;

    function let()
    {
        $this->secret = base64_rand(32);
        $this->beConstructedWith($this->secret);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ApplicationSecret::class);
    }

    function it_should_be_an_ValueObject()
    {
        $this->shouldBeAnInstanceOf(AbstractValueObject::class);
    }

    function it_should_converts_to_string()
    {
        $this->__toString()->shouldReturn($this->secret);
    }

    function it_should_compares_with_another_ApplicationSecret()
    {
        $key1 = new ApplicationSecret($this->secret);
        $key2 = new ApplicationSecret(str_repeat('1', 32));

        $this->equals($key1)->shouldBe(true);
        $this->equals($key2)->shouldBe(false);
    }

    function it_should_be_auto_generated_statically()
    {
        $this->beConstructedThrough('generate');
    }

    function it_should_be_constructed_with_32_length_string()
    {
        $this->beConstructedWith('SOMEINVALIDSECRET');
        $this->shouldThrow(InvalidArgumentException::class)->duringInstantiation();
    }

    function it_should_be_constructed_with_base64_valid_string()
    {
        $this->beConstructedWith('some_invalid_secretttttttttttttt');
        $this->shouldThrow(InvalidArgumentException::class)->duringInstantiation();
    }
}
