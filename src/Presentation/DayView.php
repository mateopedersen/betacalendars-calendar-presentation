<?php
declare(strict_types=1);

namespace BetaCalendars\CalendarPresentation\Presentation;

use BetaCalendars\CalendarPresentation\MonthRelation;

final readonly class DayView
{
    /** @param list<string> $cssTokens */
    public function __construct(
        public ?string $isoDate,
        public int $dayNumber,
        public int $row,
        public int $column,
        public MonthRelation $relation,
        public bool $isWeekend,
        public bool $isCurrentDate,
        public bool $isPlaceholder,
        public ?string $weekdayLabel,
        public ?string $shortWeekdayLabel,
        public ?string $ariaLabel,
        public array $cssTokens,
    ) {}

    public function isCurrentMonth(): bool { return $this->relation === MonthRelation::Current; }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'date' => $this->isoDate,
            'day' => $this->isoDate === null ? null : $this->dayNumber,
            'row' => $this->row,
            'column' => $this->column,
            'relation' => strtolower($this->relation->name),
            'isWeekend' => $this->isWeekend,
            'isCurrentDate' => $this->isCurrentDate,
            'isPlaceholder' => $this->isPlaceholder,
            'weekday' => $this->weekdayLabel,
            'ariaLabel' => $this->ariaLabel,
            'classes' => $this->cssTokens,
        ];
    }
}
