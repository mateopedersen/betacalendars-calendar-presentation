<?php
declare(strict_types=1);

namespace BetaCalendars\CalendarPresentation\Blank;

use BetaCalendars\CalendarPresentation\WeekStart;

final readonly class BlankCalendar
{
    /**
     * @param list<list<BlankCell>> $rows
     * @param list<string> $weekdayLabels
     */
    public function __construct(
        public int $rowCount,
        public int $columnCount,
        public array $rows,
        public WeekStart $weekStart,
        public string $locale,
        public ?string $title,
        public array $weekdayLabels,
    ) {}

    public function cellCount(): int { return $this->rowCount * $this->columnCount; }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'rows' => $this->rowCount,
            'columns' => $this->columnCount,
            'weekStartsOn' => $this->weekStart->token(),
            'locale' => $this->locale,
            'title' => $this->title,
            'weekdayLabels' => $this->weekdayLabels,
            'cells' => array_map(
                static fn (array $row): array => array_map(static fn (BlankCell $cell): array => ['row' => $cell->row, 'column' => $cell->column], $row),
                $this->rows,
            ),
        ];
    }
}
