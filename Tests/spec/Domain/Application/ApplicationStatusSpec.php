<?php

namespace spec\Modules\PublicAPI\Domain\Application;

use Modules\Core\Entities\AbstractValueObject;
use Modules\PublicAPI\Domain\Application\ApplicationStatus;
use PhpSpec\ObjectBehavior;

/**
 * Class ApplicationStatusSpec
 * @package spec\Modules\PublicAPI\Domain\Application
 * @mixin ApplicationStatus
 */
class ApplicationStatusSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedThrough('active');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ApplicationStatus::class);
    }

    function it_is_ValueObject()
    {
        $this->shouldBeAnInstanceOf(AbstractValueObject::class);
    }

    function it_should_has_code()
    {
        $this->code()->shouldBe(ApplicationStatus::STATUS_CODE_ACTIVE);
    }

    function it_should_has_date()
    {
        $this->date()->getTimestamp()->shouldBeApproximately(time(), 1);
    }

    function it_should_be_Stringify()
    {
        $this->__toString()->shouldReturn('active');
    }

    function it_can_constructs_as_active()
    {
        $this->beConstructedThrough('active', [$dateTime = new \DateTime()]);
        $this->code()->shouldBe(ApplicationStatus::STATUS_CODE_ACTIVE);
        $this->date()->shouldBe($dateTime);
        $this->__toString()->shouldReturn('active');
    }

    function it_can_constructs_as_deleted()
    {
        $this->beConstructedThrough('deleted');
        $this->code()->shouldBe(ApplicationStatus::STATUS_CODE_DELETED);
        $this->date()->getTimestamp()->shouldBeApproximately(time(), 1);
        $this->__toString()->shouldReturn('deleted');
    }

    function it_should_check_if_it_consists_of_status()
    {
        $this->is(ApplicationStatus::active())->shouldBe(true);
        $this->is(ApplicationStatus::deleted())->shouldBe(false);
    }

    function it_should_merge_with_another_status()
    {
        $status = $this->and(ApplicationStatus::deleted());

        $status->is(ApplicationStatus::active())->shouldBe(true);
        $status->is(ApplicationStatus::deleted())->shouldReturn(true);
    }

    function it_should_remove_inner_status()
    {
        $status = $this->and(ApplicationStatus::deleted())->not(ApplicationStatus::active());
        $status->is(ApplicationStatus::active())->shouldBe(false);
        $status->is(ApplicationStatus::deleted())->shouldReturn(true);

        $status = $status->not(ApplicationStatus::deleted());
        $status->is(ApplicationStatus::active())->shouldBe(false);
        $status->is(ApplicationStatus::deleted())->shouldReturn(false);
    }

    function it_should_be_constructs_from_string()
    {
        $this->beConstructedThrough('fromString', ['active']);
        $this->is(ApplicationStatus::active())->shouldBe(true);
    }

    function it_should_be_constructs_from_composite_string()
    {
        $this->beConstructedThrough('fromString', ['active,deleted']);
        $this->is(ApplicationStatus::active())->shouldBe(true);
        $this->is(ApplicationStatus::deleted())->shouldReturn(true);
    }

    function it_should_throw_exception_if_try_constructs_from_undefined_status_string()
    {
        $this->beConstructedThrough('fromString', ['active,someInvalidStatus']);
        $this->shouldThrow(\DomainException::class)->duringInstantiation();
    }
}
