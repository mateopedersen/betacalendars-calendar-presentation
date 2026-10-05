<?php

declare(strict_types=1);

namespace BetaCalendars\CalendarPresentation\Presentation;

final readonly class NavigationView
{
    public function __construct(public ?string $previousMonth, public string $currentMonth, public ?string $nextMonth)
    {
    }

    /** @return array<string, ?string> */
    public function toArray(): array
    {
        return ['previous' => $this->previousMonth, 'current' => $this->currentMonth, 'next' => $this->nextMonth];
    }
}
