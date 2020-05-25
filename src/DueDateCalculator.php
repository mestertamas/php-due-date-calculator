<?php declare(strict_types=1);

namespace mestert\DueDate;

use DateTimeInterface;
use mestert\DueDate\Traits\DateTimeTrait;
use mestert\DueDate\Validators\DueDateValidator;

class DueDateCalculator
{
    use DateTimeTrait;

    private const WORKING_HOURS_START = 9;
    private const WORKING_HOURS_END   = 17;

    /** @var DueDateValidator */
    private $validator;

    public function __construct(DueDateValidator $validator)
    {
        $this->validator = $validator;
    }

    public function calculateDueDate(DatetimeInterface $submitDate, int $turnaroundTime): DatetimeInterface
    {
        $this->validator->validateSubmitDate($submitDate, self::WORKING_HOURS_START, self::WORKING_HOURS_END);
        $this->validator->validateTurnaroundTime($turnaroundTime);

        // If no action required
        if ($turnaroundTime === 0)
        {
            return $submitDate;
        }

        // If can be done within the day
        if ($this->canBeDoneToday($submitDate, $turnaroundTime))
        {
            return $this->addHours($submitDate, $turnaroundTime);
        }

        return $this->calculate($submitDate, $turnaroundTime);
    }

    private function calculate(DatetimeInterface $submitDate, int $turnaroundTime): DatetimeInterface
    {
        $hoursToWork    = $this->calculateHoursToWork();
        $daysToWork     = (int)floor($turnaroundTime / $hoursToWork);
        $remainingHours = $turnaroundTime % $hoursToWork;

        $dueDate = $this->incrementWithDays($submitDate, $daysToWork);
        $dueDate = $this->incrementWithHours($dueDate, $remainingHours);

        return $dueDate;
    }

    private function calculateHoursToWork(): int
    {
        return self::WORKING_HOURS_END - self::WORKING_HOURS_START;
    }

    private function canBeDoneToday(DatetimeInterface $date, int $turnaroundTime): bool
    {
        $incrementedDate = $this->addHours($date, $turnaroundTime);

        if ($date->format('Y-m-d') !== $incrementedDate->format('Y-m-d'))
        {
            return false;
        }

        if (
            $incrementedDate->format('G') < self::WORKING_HOURS_START
            || $incrementedDate->format('G') > self::WORKING_HOURS_END
        ) {
            return false;
        }

        return true;
    }

    private function incrementWithDays(DatetimeInterface $date, int $days): DateTimeInterface
    {
        $nextDayDate = $date;
        $daysToWork  = $days;

        while ($daysToWork > 0)
        {
            $nextDayDate = $this->addDays($nextDayDate, 1);

            if (!$this->isWorkDay($nextDayDate))
            {
                continue;
            }

            $daysToWork--;
        }

        return $nextDayDate;
    }

    private function incrementWithHours(DateTimeInterface $date, int $hours): DateTimeInterface
    {
        $modifiedDate = $date;
        $hoursToAdd   = $hours;

        while ($hoursToAdd > 0)
        {
            $modifiedDate = $this->addHours($modifiedDate, 1);

            if (!$this->isInWorkingHours($modifiedDate, self::WORKING_HOURS_START, self::WORKING_HOURS_END))
            {
                continue;
            }

            if (!$this->isWorkDay($modifiedDate))
            {
                $modifiedDate = $this->addDays($modifiedDate, 1);
            }

            $hoursToAdd--;
        }

        return $modifiedDate;
    }
}
