<?php

namespace spec\Modules\PublicAPI\Domain\Application;

use InvalidArgumentException;
use Modules\Core\Entities\AbstractIdentity;
use Modules\PublicAPI\Domain\Application\ApplicationKey;
use PhpSpec\ObjectBehavior;
use function Modules\PublicAPI\base64_rand;

/**
 * Class ApplicationKeySpec
 * @package spec\Modules\PublicAPI\Domain\Application
 * @mixin ApplicationKey
 */
class ApplicationKeySpec extends ObjectBehavior
{
    private $key;

    function let()
    {
        $this->key = base64_rand(16);
        $this->beConstructedWith($this->key);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ApplicationKey::class);
    }

    function it_should_be_an_Identity()
    {
        $this->shouldBeAnInstanceOf(AbstractIdentity::class);
    }

    function it_should_converts_to_string()
    {
        $this->__toString()->shouldReturn($this->key);
    }

    function it_should_compares_with_another_ApplicationKey()
    {
        $key1 = new ApplicationKey($this->key);
        $key2 = new ApplicationKey(str_repeat('1', 16));

        $this->equals($key1)->shouldBe(true);
        $this->equals($key2)->shouldBe(false);
    }

    function it_should_be_auto_generated_statically()
    {
        $this->beConstructedThrough('generate');
    }

    function it_should_be_constructed_with_16_length_string()
    {
        $this->beConstructedWith('SOMEINVALIDKEY');
        $this->shouldThrow(InvalidArgumentException::class)->duringInstantiation();
    }

    function it_should_be_constructed_with_base64_valid_string()
    {
        $this->beConstructedWith('some_invalid_key');
        $this->shouldThrow(InvalidArgumentException::class)->duringInstantiation();
    }
}
