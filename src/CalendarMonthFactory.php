<?php

declare(strict_types=1);

namespace BetaCalendars\CalendarPresentation;

use BetaCalendars\CalendarPresentation\Model\CalendarDay;
use BetaCalendars\CalendarPresentation\Model\CalendarMonth;
use BetaCalendars\CalendarPresentation\Model\CalendarWeek;
use BetaCalendars\CalendarPresentation\Model\GridCoordinate;
use DateInterval;
use DateTimeImmutable;
use DateTimeZone;

final class CalendarMonthFactory
{
    public function create(
        YearMonth $month,
        WeekStart $weekStart = WeekStart::Monday,
        GridMode $mode = GridMode::Natural,
    ): CalendarMonth {
        $first = $month->firstDate();
        $firstColumn = ((int) $first->format('w') - $weekStart->value + 7) % 7;
        $rowCount = $mode === GridMode::FixedSixWeeks
            ? 6
            : (int) ceil(($firstColumn + $month->daysInMonth()) / 7);
        $start = $first->sub(new DateInterval('P' . $firstColumn . 'D'));
        $targetKey = $month->iso();
        $weeks = [];
        for ($row = 0; $row < $rowCount; $row++) {
            $days = [];
            for ($column = 0; $column < 7; $column++) {
                $date = $start->add(new DateInterval('P' . (($row * 7) + $column) . 'D'));
                $dateKey = $date->format('Y-m');
                $relation = $dateKey < $targetKey
                    ? MonthRelation::Previous
                    : ($dateKey > $targetKey ? MonthRelation::Next : MonthRelation::Current);
                $days[] = new CalendarDay(
                    $date->setTimezone(new DateTimeZone('UTC')),
                    new GridCoordinate($row, $column),
                    $relation,
                );
            }
            $weeks[] = new CalendarWeek($row, $days);
        }
        return new CalendarMonth($month, $weekStart, $mode, $weeks);
    }
}
