<?php

declare(strict_types=1);

namespace BetaCalendars\CalendarPresentation\Tests;

use BetaCalendars\CalendarPresentation\AdjacentMonthPolicy;
use BetaCalendars\CalendarPresentation\Blank\BlankCalendarFactory;
use BetaCalendars\CalendarPresentation\CalendarMonthFactory;
use BetaCalendars\CalendarPresentation\GridMode;
use BetaCalendars\CalendarPresentation\Localization\CalendarLocale;
use BetaCalendars\CalendarPresentation\Localization\InvalidLocaleException;
use BetaCalendars\CalendarPresentation\MonthRelation;
use BetaCalendars\CalendarPresentation\Navigation\MonthNavigation;
use BetaCalendars\CalendarPresentation\Presentation\MonthViewFactory;
use BetaCalendars\CalendarPresentation\Rendering\Html\HtmlCalendarRenderer;
use BetaCalendars\CalendarPresentation\Rendering\Html\HtmlRenderOptions;
use BetaCalendars\CalendarPresentation\Rendering\Json\CalendarJsonRenderer;
use BetaCalendars\CalendarPresentation\WeekStart;
use BetaCalendars\CalendarPresentation\YearMonth;
use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class CalendarPresentationTest extends TestCase
{
    public function testYearMonthParsingAndArithmeticAcrossYearBoundary(): void
    {
        $december = YearMonth::fromString('2026-12');
        self::assertSame('2027-01', $december->next()->iso());
        self::assertSame('2026-11', $december->previous()->iso());
        self::assertSame('2027-03', $december->plusMonths(3)->iso());
        self::assertSame(31, $december->daysInMonth());
        self::assertSame('2026-12-31', $december->lastDate()->format('Y-m-d'));
    }

    public function testInvalidMonthsAreRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        YearMonth::fromString('2026-13');
    }

    public function testMonthArithmeticRejectsValuesOutsideSupportedRange(): void
    {
        $this->expectException(InvalidArgumentException::class);
        YearMonth::of(1, 1)->plusMonths(PHP_INT_MAX);
    }

    public function testNaturalAndFixedGridSizes(): void
    {
        $factory = new CalendarMonthFactory();
        $natural = $factory->create(YearMonth::of(2027, 2), WeekStart::Monday);
        $fixed = $factory->create(YearMonth::of(2027, 2), WeekStart::Monday, GridMode::FixedSixWeeks);
        self::assertSame(4, $natural->rowCount());
        self::assertSame(6, $fixed->rowCount());
        self::assertCount(42, array_merge(...array_map(static fn ($week): array => $week->days, $fixed->weeks)));
    }

    public function testReferenceMonthsHaveExpectedDayCountsAndBoundaries(): void
    {
        $factory = new CalendarMonthFactory();
        foreach ([[2026, 11, 30], [2026, 12, 31], [2027, 1, 31], [2027, 2, 28]] as [$year, $month, $days]) {
            $model = $factory->create(YearMonth::of($year, $month));
            self::assertSame($days, $model->dayCount());
            $currentDays = [];
            foreach ($model->weeks as $week) {
                foreach ($week->days as $day) {
                    if ($day->relation === MonthRelation::Current) {
                        $currentDays[] = $day->date->format('j');
                    }
                }
            }
            self::assertSame(range(1, $days), array_map('intval', $currentDays));
        }
    }

    public function testEveryWeekStartProducesSevenColumns(): void
    {
        $factory = new CalendarMonthFactory();
        foreach (WeekStart::cases() as $start) {
            $model = $factory->create(YearMonth::of(2026, 11), $start);
            foreach ($model->weeks as $row => $week) {
                self::assertCount(7, $week->days);
                foreach ($week->days as $column => $day) {
                    self::assertSame($row, $day->coordinate->row);
                    self::assertSame($column, $day->coordinate->column);
                }
            }
        }
    }

    public function testCalendarDoesNotDependOnDefaultTimezone(): void
    {
        $previous = date_default_timezone_get();
        date_default_timezone_set('Pacific/Kiritimati');
        try {
            self::assertSame('2027-01-01', YearMonth::of(2027, 1)->firstDate()->format('Y-m-d'));
        } finally {
            date_default_timezone_set($previous);
        }
    }

    public function testAdjacentMonthPoliciesPreserveStructureWithoutInventingVisibleDates(): void
    {
        $factory = new MonthViewFactory();
        $month = YearMonth::of(2027, 1);
        $show = $factory->create($month, adjacentMonths: AdjacentMonthPolicy::Show);
        $hide = $factory->create($month, adjacentMonths: AdjacentMonthPolicy::Hide);
        $placeholder = $factory->create($month, adjacentMonths: AdjacentMonthPolicy::Placeholder);
        $showDays = array_merge(...array_map(static fn ($week): array => $week->days, $show->weeks));
        $hideDays = array_merge(...array_map(static fn ($week): array => $week->days, $hide->weeks));
        $placeholderDays = array_merge(...array_map(static fn ($week): array => $week->days, $placeholder->weeks));
        self::assertSame('2026-12-28', $showDays[0]->isoDate);
        self::assertNull($hideDays[0]->isoDate);
        self::assertNull($placeholderDays[0]->isoDate);
        self::assertTrue($placeholderDays[0]->isPlaceholder);
        self::assertFalse($hideDays[0]->isPlaceholder);
    }

    public function testExplicitCurrentDateIsMarkedWithoutUsingTheClock(): void
    {
        $view = (new MonthViewFactory())->create(
            YearMonth::of(2027, 1),
            currentDate: new DateTimeImmutable('2027-01-12 23:30:00', new DateTimeZone('Pacific/Honolulu')),
        );
        $days = array_merge(...array_map(static fn ($week): array => $week->days, $view->weeks));
        $today = array_values(array_filter($days, static fn ($day): bool => $day->isCurrentDate));
        self::assertCount(1, $today);
        self::assertSame('2027-01-13', $today[0]->isoDate);
    }

    public function testIntlLocalesProduceLabelsWhenExtensionExists(): void
    {
        if (!class_exists(\IntlDateFormatter::class)) {
            self::markTestSkipped('ext-intl is optional and is not installed.');
        }
        $cases = [
            ['en_US', 'January'],
            ['tr_TR', 'Ocak'],
            ['de_DE', 'Januar'],
            ['fr_FR', 'janvier'],
            ['ja_JP', '1月'],
        ];
        foreach ($cases as [$locale, $expected]) {
            $view = (new MonthViewFactory())->create(YearMonth::of(2027, 1), new CalendarLocale($locale));
            self::assertStringContainsString($expected, $view->label);
            self::assertStringContainsString('2027', $view->label);
            self::assertCount(7, $view->weekdayHeaders);
        }
    }

    public function testNonEnglishLocaleRequiresIntlExtension(): void
    {
        if (class_exists(\IntlDateFormatter::class)) {
            self::markTestSkipped('ext-intl is installed.');
        }
        $this->expectException(InvalidLocaleException::class);
        (new MonthViewFactory())->create(YearMonth::of(2027, 1), new CalendarLocale('tr_TR'));
    }

    public function testHtmlIsSemanticEscapedAndContainsNoExecutableAssets(): void
    {
        $view = (new MonthViewFactory())->create(YearMonth::of(2027, 1));
        $options = new HtmlRenderOptions('<script>alert("x")</script> & "', 'my-calendar');
        $html = (new HtmlCalendarRenderer())->render($view, $options);
        self::assertStringContainsString('&lt;script&gt;alert(&quot;x&quot;)&lt;/script&gt; &amp; &quot;', $html);
        self::assertStringContainsString('<caption', $html);
        self::assertStringContainsString('<th id="my-calendar-weekday-0" scope="col"', $html);
        self::assertStringContainsString('headers="my-calendar-weekday-', $html);
        self::assertStringNotContainsString('<script>', $html);
        self::assertStringNotContainsString('<img', $html);
        self::assertStringNotContainsString('javascript:', $html);
        self::assertStringNotContainsString('https://', $html);
    }

    public function testHtmlClassPrefixMustBeSafe(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new HtmlRenderOptions(classPrefix: 'x" onclick="alert(1)');
    }

    public function testJsonIsStableAndContainsStructuralMetadata(): void
    {
        $view = (new MonthViewFactory())->create(YearMonth::of(2027, 1));
        $renderer = new CalendarJsonRenderer();
        $json = $renderer->render($view);
        self::assertSame($json, $renderer->render($view));
        self::assertSame('2027-01', json_decode($json, true, flags: JSON_THROW_ON_ERROR)['month']);
        self::assertStringContainsString('"weekStartsOn":"monday"', $json);
    }

    public function testGoldenSnapshotsMatchMonthJsonHtmlAndBlankGridOutputs(): void
    {
        $viewFactory = new MonthViewFactory();
        $json = new CalendarJsonRenderer();
        $months = [
            'november-2026' => [YearMonth::of(2026, 11), new CalendarLocale('en_US')],
            'december-2026' => [YearMonth::of(2026, 12), new CalendarLocale('en_US')],
            'january-2027' => [YearMonth::of(2027, 1), new CalendarLocale('en_US')],
            'february-2027' => [YearMonth::of(2027, 2), new CalendarLocale('en_US')],
        ];
        foreach ($months as $name => [$month, $locale]) {
            $view = $viewFactory->create($month, $locale);
            self::assertSame($this->snapshot($name . '.json'), $json->render($view) . "\n");
        }

        if (class_exists(\IntlDateFormatter::class)) {
            $turkish = $viewFactory->create(YearMonth::of(2027, 1), new CalendarLocale('tr_TR'));
            $japanese = $viewFactory->create(YearMonth::of(2027, 2), new CalendarLocale('ja_JP'));
            self::assertSame($this->snapshot('january-2027-tr.json'), $json->render($turkish) . "\n");
            self::assertSame($this->snapshot('february-2027-ja.json'), $json->render($japanese) . "\n");
        }

        $blank = (new BlankCalendarFactory())->create(rows: 6, columns: 7, weekStart: WeekStart::Monday);
        $blankJson = json_encode($blank->toArray(), JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        self::assertSame($this->snapshot('blank-6x7.json'), $blankJson . "\n");

        $january = $viewFactory->create(YearMonth::of(2027, 1));
        $html = (new HtmlCalendarRenderer())->render($january) . "\n";
        self::assertSame($this->snapshot('january-2027.html'), $html);
    }

    private function snapshot(string $file): string
    {
        $contents = file_get_contents(__DIR__ . '/snapshots/' . $file);
        self::assertIsString($contents, 'Snapshot file must be readable: ' . $file);
        return $contents;
    }

    public function testBlankCalendarHasUniqueCoordinatesAndNoDates(): void
    {
        $blank = (new BlankCalendarFactory())->create(rows: 6, columns: 7, weekStart: WeekStart::Sunday);
        self::assertSame(42, $blank->cellCount());
        self::assertCount(7, $blank->weekdayLabels);
        $coordinates = [];
        foreach ($blank->rows as $row) {
            foreach ($row as $cell) {
                $coordinates[] = $cell->row . ':' . $cell->column;
            }
        }
        self::assertCount(42, array_unique($coordinates));
        self::assertArrayNotHasKey('date', $blank->toArray());
    }

    public function testBlankCalendarRejectsUnreasonableDimensions(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new BlankCalendarFactory())->create(rows: 0);
    }

    public function testNavigationHonorsBoundaries(): void
    {
        $navigation = MonthNavigation::for(YearMonth::of(2027, 1), YearMonth::of(2027, 1), YearMonth::of(2027, 2));
        self::assertNull($navigation->previous());
        self::assertSame('2027-02', $navigation->next()?->iso());
    }

    public function testNavigationRejectsCurrentOutsideRange(): void
    {
        $this->expectException(InvalidArgumentException::class);
        MonthNavigation::for(YearMonth::of(2027, 3), maximum: YearMonth::of(2027, 2));
    }

    public function testAllMonthsFrom1900Through2100MaintainGridInvariants(): void
    {
        $factory = new CalendarMonthFactory();
        for ($year = 1900; $year <= 2100; $year++) {
            for ($month = 1; $month <= 12; $month++) {
                $monthValue = YearMonth::of($year, $month);
                $model = $factory->create($monthValue, WeekStart::Monday);
                $fixed = $factory->create($monthValue, WeekStart::Monday, GridMode::FixedSixWeeks);
                self::assertCount(6, $fixed->weeks);
                $fixedDays = array_merge(...array_map(static fn ($week): array => $week->days, $fixed->weeks));
                self::assertCount(42, $fixedDays);
                $dates = [];
                foreach ($model->weeks as $row => $week) {
                    self::assertCount(7, $week->days);
                    foreach ($week->days as $column => $day) {
                        self::assertSame($row, $day->coordinate->row);
                        self::assertSame($column, $day->coordinate->column);
                        if ($day->isCurrentMonth()) {
                            $dates[] = $day->date->format('Y-m-d');
                        }
                    }
                }
                self::assertCount($monthValue->daysInMonth(), $dates);
                self::assertCount(count(array_unique($dates)), $dates);
                self::assertSame($monthValue->firstDate()->format('Y-m-d'), $dates[0]);
                self::assertSame($monthValue->lastDate()->format('Y-m-d'), $dates[array_key_last($dates)]);
            }
        }
    }
}
