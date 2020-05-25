<?php declare(strict_types=1);

namespace Unit;

use DateInterval;
use DateTimeImmutable;
use InvalidArgumentException;
use mestert\DueDate\Validators\DueDateValidator;
use PHPUnit\Framework\TestCase;

/** @coversDefaultClass \mestert\DueDate\Validators\DueDateValidator */
class DueDateValidatorTest extends TestCase
{
    /** @var DueDateValidator */
    private $sut;

    protected function setUp(): void
    {
        $this->sut = new DueDateValidator();
    }

    /**
     * @covers ::validateSubmitDate
     */
    public function testValidateSubmitDateThrowsExceptionOnFutureDate(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Reported in the future!');

        $currentDate = new DateTimeImmutable();
        $futureDate  = $currentDate->add(new DateInterval('PT1H'));

        $this->sut->validateSubmitDate($futureDate, 9, 17);
    }

    /**
     * @covers ::validateSubmitDate
     */
    public function testValidateSubmitDateThrowsExceptionOnNonWorkDay(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Reported in a non working day!');

        $weekendDay = new DateTimeImmutable('sunday last week');

        $this->sut->validateSubmitDate($weekendDay, 9, 17);
    }

    /**
     * @covers ::validateSubmitDate
     */
    public function testValidateSubmitDateThrowsExceptionOnNonWorkingHours(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Reported in non working hours!');

        $currentDate = new DateTimeImmutable('monday last week');
        $modified    = $currentDate->setTime(20, 20, 00);

        $this->sut->validateSubmitDate($modified, 9, 17);
    }

    /**
     * @covers ::validateTurnaroundTime
     */
    public function testValidateValidateTurnaroundTimeThrowsExceptionOnNegativeInteger(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Turnaround time should be greater than zero!');

        $this->sut->validateTurnaroundTime(-1);
    }
}
