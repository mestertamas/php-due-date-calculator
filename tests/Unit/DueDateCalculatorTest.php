<?php declare(strict_types=1);

namespace Unit;

use DateInterval;
use DateTimeImmutable;
use mestert\DueDate\DueDateCalculator;
use mestert\DueDate\Validators\DueDateValidator;
use PHPUnit\Framework\TestCase;

/** @coversDefaultClass \mestert\DueDate\DueDateCalculator */
class DueDateCalculatorTest extends TestCase
{
    /** @var DueDateCalculator */
    private $sut;

    protected function setUp(): void
    {
        $this->sut = new DueDateCalculator(new DueDateValidator());
    }

    /**
     * @covers ::calculateDueDate
     */
    public function testCalculateDueDateWillReturnTheSameDate(): void
    {
        $currentDate = new DateTimeImmutable();

        $this->assertEquals($this->sut->calculateDueDate($currentDate, 0), $currentDate);
    }

    /**
     * @covers ::calculateDueDate
     */
    public function testCalculateDueDateWillReturnDailyWork(): void
    {
        $currentDate    = new DateTimeImmutable('monday last week');
        $modified       = $currentDate->setTime(9, 00, 00);
        $dueDate        = $modified->setTime(13, 00, 00);
        $turnaroundTime = 4;

        $this->assertEquals($this->sut->calculateDueDate($modified, $turnaroundTime), $dueDate);
    }

    /**
     * @covers ::calculateDueDate
     */
    public function testCalculateDueDateWillAddNonWorkingHours(): void
    {
        $currentDate    = new DateTimeImmutable('monday last week');
        $modified       = $currentDate->setTime(16, 00, 00);
        $nextDay        = new DateTimeImmutable('tuesday last week');
        $dueDate        = $nextDay->setTime(12, 00, 00);
        $turnaroundTime = 4;

        $this->assertEquals($this->sut->calculateDueDate($modified, $turnaroundTime), $dueDate);
    }

    /**
     * @covers ::calculateDueDate
     */
    public function testCalculateDueDateWillAddWeekendDays(): void
    {
        $currentDate    = new DateTimeImmutable('friday last week');
        $twoWeeksAgo    = $currentDate->sub(new DateInterval('P7D'));
        $modified       = $twoWeeksAgo->setTime(16, 00, 00);
        $lastWeek       = new DateTimeImmutable('monday last week');
        $dueDate        = $lastWeek->setTime(12, 00, 00);
        $turnaroundTime = 4;

        $this->assertEquals($this->sut->calculateDueDate($modified, $turnaroundTime), $dueDate);
    }

    /**
     * @covers ::isFutureDate
     */
    public function testIsFutureDateReturnsTrue(): void
    {
        $currentDate = new DateTimeImmutable();
        $futureDate  = $currentDate->add(new DateInterval('PT1H'));

        $this->assertTrue($this->sut->isFutureDate($futureDate));
    }

    /**
     * @covers ::isFutureDate
     */
    public function testIsFutureDateReturnsFalse(): void
    {
        $currentDate = new DateTimeImmutable();

        $this->assertFalse($this->sut->isFutureDate($currentDate));
    }

    /**
     * @covers ::isWorkDay
     */
    public function testIsWorkDayReturnsTrue(): void
    {
        $currentDate = new DateTimeImmutable();

        $this->assertTrue($this->sut->isWorkDay($currentDate));
    }

    /**
     * @covers ::isWorkDay
     */
    public function testIsWorkDayReturnsFalse(): void
    {
        $weekendDay = new DateTimeImmutable('sunday last week');

        $this->assertFalse($this->sut->isWorkDay($weekendDay));
    }

    /**
     * @covers ::isInWorkingHours
     */
    public function testIsInWorkingHoursReturnsTrue(): void
    {
        $currentDate = new DateTimeImmutable();
        $modified    = $currentDate->setTime(10, 10, 00);

        $this->assertTrue($this->sut->isInWorkingHours($modified, 9, 17));
    }

    /**
     * @covers ::isInWorkingHours
     */
    public function testIsInWorkingHoursReturnsFalse(): void
    {
        $currentDate = new DateTimeImmutable();
        $modified    = $currentDate->setTime(20, 20, 00);

        $this->assertFalse($this->sut->isInWorkingHours($modified, 9, 17));
    }

    /**
     * @covers ::addHours
     */
    public function testAddHoursReturnsProperDateTime(): void
    {
        $currentDate = new DateTimeImmutable();
        $hoursToAdd  = 2;
        $modified    = $currentDate->setTime(20, 00, 00);
        $hoursAdded  = $modified->add(new DateInterval(sprintf('PT%dH', $hoursToAdd)));

        $this->assertEquals($this->sut->addHours($modified, $hoursToAdd), $hoursAdded);
    }

    /**
     * @covers ::addDays
     */
    public function testAddDaysReturnsProperDateTime(): void
    {
        $currentDate = new DateTimeImmutable();
        $daysToAdd   = 2;
        $modified    = $currentDate->setTime(20, 00, 00);
        $daysAdded   = $modified->add(new DateInterval(sprintf('P%dD', $daysToAdd)));

        $this->assertEquals($this->sut->addDays($modified, $daysToAdd), $daysAdded);
    }
}
