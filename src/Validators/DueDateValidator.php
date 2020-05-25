<?php declare(strict_types=1);

namespace mestert\DueDate\Validators;

use DateTimeInterface;
use InvalidArgumentException;
use mestert\DueDate\Traits\DateTimeTrait;

class DueDateValidator
{
    use DateTimeTrait;

    /**
     * @throws InvalidArgumentException
     */
    public function validateSubmitDate(DatetimeInterface $submitDate, int $startHour, int $stopHour): void
    {
        if ($this->isFutureDate($submitDate))
        {
            throw new InvalidArgumentException('Reported in the future!');
        }

        if (!$this->isWorkDay($submitDate))
        {
            throw new InvalidArgumentException('Reported in a non working day!');
        }

        if (!$this->isInWorkingHours($submitDate, $startHour, $stopHour))
        {
            throw new InvalidArgumentException('Reported in non working hours!');
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    public function validateTurnaroundTime(int $turnaroundTime): void
    {
        if ($turnaroundTime < 0)
        {
            throw new InvalidArgumentException('Turnaround time should be greater than zero!');
        }
    }
}
