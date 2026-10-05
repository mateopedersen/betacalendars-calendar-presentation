<?php
declare(strict_types=1);

namespace BetaCalendars\CalendarPresentation\Model;

use BetaCalendars\CalendarPresentation\MonthRelation;
use DateTimeImmutable;

final readonly class CalendarDay
{
    public function __construct(
        public DateTimeImmutable $date,
        public GridCoordinate $coordinate,
        public MonthRelation $relation,
    ) {}

    public function isCurrentMonth(): bool { return $this->relation === MonthRelation::Current; }
}
