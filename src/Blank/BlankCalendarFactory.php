<?php
declare(strict_types=1);

namespace BetaCalendars\CalendarPresentation\Blank;

use BetaCalendars\CalendarPresentation\Localization\CalendarLocale;
use BetaCalendars\CalendarPresentation\Localization\LocaleFormatter;
use BetaCalendars\CalendarPresentation\WeekStart;
use DateTimeImmutable;
use DateTimeZone;

final class BlankCalendarFactory
{
    public function __construct(private LocaleFormatter $formatter = new LocaleFormatter()) {}

    public function create(
        int $rows = 6,
        int $columns = 7,
        WeekStart $weekStart = WeekStart::Monday,
        CalendarLocale $locale = new CalendarLocale('en_US'),
        ?string $title = null,
        bool $includeWeekdayLabels = true,
    ): BlankCalendar {
        if ($rows < 1 || $rows > 52 || $columns < 1 || $columns > 14) {
            throw new \InvalidArgumentException('Blank grids must have 1–52 rows and 1–14 columns.');
        }
        $grid = [];
        for ($row = 0; $row < $rows; $row++) {
            $cells = [];
            for ($column = 0; $column < $columns; $column++) { $cells[] = new BlankCell($row, $column); }
            $grid[] = $cells;
        }
        $labels = [];
        if ($includeWeekdayLabels && $columns === 7) {
            $sunday = new DateTimeImmutable('2024-01-07 00:00:00', new DateTimeZone('UTC'));
            for ($column = 0; $column < 7; $column++) {
                $date = $sunday->modify('+' . (($weekStart->value + $column) % 7) . ' days');
                $labels[] = $this->formatter->format($date, $locale->id, 'EEE');
            }
        }
        return new BlankCalendar($rows, $columns, $grid, $weekStart, $locale->id, $title, $labels);
    }
}
