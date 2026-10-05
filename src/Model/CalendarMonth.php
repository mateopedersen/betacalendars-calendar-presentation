<?php
declare(strict_types=1);

namespace BetaCalendars\CalendarPresentation\Model;

use BetaCalendars\CalendarPresentation\GridMode;
use BetaCalendars\CalendarPresentation\WeekStart;
use BetaCalendars\CalendarPresentation\YearMonth;

final readonly class CalendarMonth
{
    /** @param list<CalendarWeek> $weeks */
    public function __construct(
        public YearMonth $month,
        public WeekStart $weekStart,
        public GridMode $gridMode,
        public array $weeks,
    ) {}

    public function rowCount(): int { return count($this->weeks); }
    public function dayCount(): int { return $this->month->daysInMonth(); }
}
