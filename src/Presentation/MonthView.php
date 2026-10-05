<?php
declare(strict_types=1);

namespace BetaCalendars\CalendarPresentation\Presentation;

use BetaCalendars\CalendarPresentation\WeekStart;

final readonly class MonthView
{
    /**
     * @param list<array{label: string, short: string, narrow: string}> $weekdayHeaders
     * @param list<WeekView> $weeks
     */
    public function __construct(
        public string $month,
        public int $year,
        public int $numericMonth,
        public string $label,
        public string $shortLabel,
        public string $locale,
        public WeekStart $weekStart,
        public array $weekdayHeaders,
        public array $weeks,
        public NavigationView $navigation,
        public string $ariaLabel,
    ) {}

    public function rowCount(): int { return count($this->weeks); }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'month' => $this->month,
            'year' => $this->year,
            'numericMonth' => $this->numericMonth,
            'label' => $this->label,
            'shortLabel' => $this->shortLabel,
            'locale' => $this->locale,
            'weekStartsOn' => $this->weekStart->token(),
            'weekdayHeaders' => $this->weekdayHeaders,
            'rowCount' => $this->rowCount(),
            'navigation' => $this->navigation->toArray(),
            'ariaLabel' => $this->ariaLabel,
            'weeks' => array_map(static fn (WeekView $week): array => $week->toArray(), $this->weeks),
        ];
    }
}
