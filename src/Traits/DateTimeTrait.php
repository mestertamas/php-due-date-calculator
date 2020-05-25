<?php declare(strict_types=1);

namespace mestert\DueDate\Traits;

use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;

trait DateTimeTrait
{
    /** @var array */
    private $workDaysMap = [
        1 => 'Monday',
        2 => 'Tuesday',
        3 => 'Wednesday',
        4 => 'Thursday',
        5 => 'Friday',
    ];

    public function isFutureDate(DatetimeInterface $date): bool
    {
        return $date > new DateTimeImmutable('now', $date->getTimezone());
    }

    public function isWorkDay(DatetimeInterface $date): bool
    {
        $day = $date->format('N');

        return array_key_exists($day, $this->workDaysMap);
    }

    public function isInWorkingHours(DatetimeInterface $date, int $startHour, $stopHour): bool
    {
        $baseDateTime         = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $date->format('Y-m-d 00:00:00'), $date->getTimezone());
        $workDayStartDateTime = $this->addHours($baseDateTime, $startHour);
        $workDayEndDateTime   = $this->addHours($baseDateTime, $stopHour);

        return $date >= $workDayStartDateTime && $date < $workDayEndDateTime;
    }

    public function addHours(DatetimeInterface $date, int $hours): DatetimeInterface
    {
        return $date->add(new DateInterval(sprintf('PT%dH', $hours)));
    }

    public function addDays(DatetimeInterface $date, int $days): DatetimeInterface
    {
        return $date->add(new DateInterval(sprintf('P%dD', $days)));
    }
}
