<?php

namespace spec\Modules\PublicAPI\Domain;

use Modules\Core\Entities\AbstractValueObject;
use Modules\PublicAPI\Domain\PersonalToken;
use Modules\PublicAPI\Services\Base64JWTProvider;
use PhpSpec\ObjectBehavior;
use Tymon\JWTAuth\JWT;

/**
 * Class PersonalTokenSpec
 * @package spec\Modules\PublicAPI\Domain
 * @mixin PersonalToken
 */
class PersonalTokenSpec extends ObjectBehavior
{
    private $token;

    function let()
    {
        $this->token = 'some_long_token';
        $this->beConstructedWith($this->token);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PersonalToken::class);
    }

    function it_should_be_an_Value_Object()
    {
        $this->shouldBeAnInstanceOf(AbstractValueObject::class);
    }

    function it_can_be_constructed_as_expired_token()
    {
        $expiredAt = time() + 3600;
        $this->beConstructedWith($this->token, $expiredAt);

        $this->expiredAt()->shouldBe($expiredAt);
    }

    function it_should_be_constructed_as_never_expired_token()
    {
        $this->expiredAt()->shouldBe(PersonalToken::EXPIRED_NEVER);
        $this->isExpired()->shouldReturn(false);
    }

    function it_should_be_constructed_from_jwt_token()
    {
        $jwtToken = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiZXhwIjoxNjE5MTc1MDE0LCJpYXQiOjE1MTYyMzkwMjIsImp0aSI6InF3ZXJ0eTEyMzQ1NiJ9.AcGpA_sd3CmgsQKm42lNjjHzSViTxasoqokOQJQlSEA';
        /**
         * @var PersonalToken $token
         */
        $this->beConstructedThrough('fromJwt', [$jwtToken, new Base64JWTProvider()]);

        $this->__toString()->shouldReturn($jwtToken);
        $this->expiredAt()->shouldBe(1619175014);
        $this->hashCode()->shouldBe('qwerty123456');
    }

    function it_should_check_if_token_is_not_expired()
    {
        $expiredAt = time() + 3600;
        $this->beConstructedWith($this->token, $expiredAt);

        $this->isExpired()->shouldReturn(false);
    }

    function it_should_check_if_token_is_expired()
    {
        $expiredAt = time() - 3600;
        $this->beConstructedWith($this->token, $expiredAt);

        $this->isExpired()->shouldReturn(true);
    }

    function it_should_stringify_to_token()
    {
        $this->__toString()->shouldBe($this->token);
    }
}
