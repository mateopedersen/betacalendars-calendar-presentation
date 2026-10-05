<?php
declare(strict_types=1);

namespace BetaCalendars\CalendarPresentation\Model;

final readonly class CalendarWeek
{
    /** @param list<CalendarDay> $days */
    public function __construct(public int $row, public array $days)
    {
        if (count($days) !== 7) {
            throw new \InvalidArgumentException('A calendar week must contain exactly seven cells.');
        }
    }
}
