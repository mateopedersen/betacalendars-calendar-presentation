<?php

declare(strict_types=1);

namespace BetaCalendars\CalendarPresentation\Presentation;

final readonly class WeekView
{
    /** @param list<DayView> $days */
    public function __construct(public int $row, public array $days)
    {
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'row' => $this->row,
            'days' => array_map(static fn (DayView $day): array => $day->toArray(), $this->days),
        ];
    }
}
