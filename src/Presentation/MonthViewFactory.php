<?php
declare(strict_types=1);

namespace BetaCalendars\CalendarPresentation\Presentation;

use BetaCalendars\CalendarPresentation\AdjacentMonthPolicy;
use BetaCalendars\CalendarPresentation\CalendarMonthFactory;
use BetaCalendars\CalendarPresentation\GridMode;
use BetaCalendars\CalendarPresentation\Localization\CalendarLocale;
use BetaCalendars\CalendarPresentation\Localization\LocaleFormatter;
use BetaCalendars\CalendarPresentation\Model\CalendarDay;
use BetaCalendars\CalendarPresentation\MonthRelation;
use BetaCalendars\CalendarPresentation\WeekStart;
use BetaCalendars\CalendarPresentation\YearMonth;
use DateTimeImmutable;
use DateTimeZone;

final class MonthViewFactory
{
    public function __construct(
        private CalendarMonthFactory $calendarFactory = new CalendarMonthFactory(),
        private LocaleFormatter $formatter = new LocaleFormatter(),
    ) {}

    public function create(
        YearMonth $month,
        CalendarLocale $locale = new CalendarLocale('en_US'),
        GridMode $mode = GridMode::Natural,
        AdjacentMonthPolicy $adjacentMonths = AdjacentMonthPolicy::Show,
        ?DateTimeImmutable $currentDate = null,
        ?WeekStart $weekStart = null,
    ): MonthView {
        $weekStart ??= $locale->weekStart;
        $calendar = $this->calendarFactory->create($month, $weekStart, $mode);
        $headers = [];
        $sunday = new DateTimeImmutable('2024-01-07 00:00:00', new DateTimeZone('UTC'));
        for ($column = 0; $column < 7; $column++) {
            $weekday = $sunday->modify('+' . (($weekStart->value + $column) % 7) . ' days');
            $headers[] = [
                'label' => $this->formatter->format($weekday, $locale->id, 'EEEE'),
                'short' => $this->formatter->format($weekday, $locale->id, 'EEE'),
                'narrow' => $this->formatter->format($weekday, $locale->id, 'EEEEE'),
            ];
        }

        $weeks = [];
        foreach ($calendar->weeks as $week) {
            $days = [];
            foreach ($week->days as $day) {
                $days[] = $this->dayView($day, $locale, $adjacentMonths, $currentDate);
            }
            $weeks[] = new WeekView($week->row, $days);
        }

        $first = $month->firstDate();
        $label = $this->formatter->format($first, $locale->id, 'MMMM y');
        return new MonthView(
            $month->iso(),
            $month->year,
            $month->month,
            $label,
            $this->formatter->format($first, $locale->id, 'MMM y'),
            $locale->id,
            $weekStart,
            $headers,
            $weeks,
            $this->navigation($month),
            $label,
        );
    }

    private function dayView(
        CalendarDay $day,
        CalendarLocale $locale,
        AdjacentMonthPolicy $policy,
        ?DateTimeImmutable $currentDate,
    ): DayView {
        $isCurrentMonth = $day->relation === MonthRelation::Current;
        $showDate = $isCurrentMonth || $policy === AdjacentMonthPolicy::Show;
        $date = $day->date->setTimezone(new DateTimeZone('UTC'));
        $sameCurrentDate = $currentDate !== null
            && $date->format('Y-m-d') === $currentDate->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d');
        $classes = ['calendar__day'];
        if (!$isCurrentMonth) { $classes[] = 'calendar__day--outside'; }
        if ((int) $date->format('w') === 0 || (int) $date->format('w') === 6) { $classes[] = 'calendar__day--weekend'; }
        if ($sameCurrentDate) { $classes[] = 'calendar__day--today'; }
        if (!$showDate && $policy === AdjacentMonthPolicy::Placeholder) { $classes[] = 'calendar__day--placeholder'; }

        $visible = $showDate;
        return new DayView(
            $visible ? $date->format('Y-m-d') : null,
            $visible ? (int) $date->format('j') : 0,
            $day->coordinate->row,
            $day->coordinate->column,
            $day->relation,
            (int) $date->format('w') === 0 || (int) $date->format('w') === 6,
            $sameCurrentDate,
            !$visible && $policy === AdjacentMonthPolicy::Placeholder,
            $visible ? $this->formatter->format($date, $locale->id, 'EEEE') : null,
            $visible ? $this->formatter->format($date, $locale->id, 'EEE') : null,
            $visible ? $this->formatter->format($date, $locale->id, 'EEEE, MMMM d, y') : null,
            $classes,
        );
    }

    private function navigation(YearMonth $month): NavigationView
    {
        try { $previous = $month->previous()->iso(); } catch (\InvalidArgumentException) { $previous = null; }
        try { $next = $month->next()->iso(); } catch (\InvalidArgumentException) { $next = null; }
        return new NavigationView($previous, $month->iso(), $next);
    }
}
