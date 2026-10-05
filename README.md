# BetaCalendars Calendar Presentation

[![CI](https://github.com/mateopedersen/betacalendars-calendar-presentation/actions/workflows/ci.yml/badge.svg)](https://github.com/mateopedersen/betacalendars-calendar-presentation/actions/workflows/ci.yml)

A framework-agnostic PHP library that turns a civil month into immutable, localized presentation data. It sits between date-grid calculations and your templates: month and day view models, weekday headers, navigation, accessibility labels, undated planner grids, and optional semantic HTML or deterministic JSON.

It does not implement an interactive date picker, depend on a template framework, fetch remote pages, or contact Beta Calendars at runtime.

## Requirements and installation

- PHP 8.3 or newer
- `ext-intl` is optional. With it, month, weekday, and accessible date labels use ICU. Without it, English labels are available; requesting another locale raises `InvalidLocaleException` instead of silently returning the wrong language.

```sh
composer require betacalendars/calendar-presentation
```

## Build a month view

```php
use BetaCalendars\CalendarPresentation\Presentation\MonthViewFactory;
use BetaCalendars\CalendarPresentation\WeekStart;
use BetaCalendars\CalendarPresentation\YearMonth;

$view = (new MonthViewFactory())->create(
    YearMonth::of(2027, 1),
    weekStart: WeekStart::Monday,
);

echo $view->label;                 // January 2027
echo $view->weeks[0]->days[0]->isoDate; // 2026-12-28
```

`YearMonth` uses explicit civil year/month values and UTC-backed immutable dates, so the output does not depend on the host timezone. `GridMode::Natural` produces four to six rows; `GridMode::FixedSixWeeks` always produces 42 cells. All seven week starts are supported.

## Presentation choices

```php
use BetaCalendars\CalendarPresentation\AdjacentMonthPolicy;
use BetaCalendars\CalendarPresentation\GridMode;
use BetaCalendars\CalendarPresentation\Localization\CalendarLocale;

$view = (new MonthViewFactory())->create(
    YearMonth::of(2027, 1),
    locale: new CalendarLocale('tr_TR'),
    mode: GridMode::FixedSixWeeks,
    adjacentMonths: AdjacentMonthPolicy::Placeholder,
    currentDate: new DateTimeImmutable('2027-01-15', new DateTimeZone('UTC')),
);
```

Adjacent cells can show their dates, hide them, or remain undated placeholders. Current-date marking is opt-in; the package never consults the system clock. See [localization](docs/localization.md), [accessibility](docs/accessibility.md), and [rendering](docs/rendering.md).

## Render HTML or JSON

```php
use BetaCalendars\CalendarPresentation\Rendering\Html\HtmlCalendarRenderer;
use BetaCalendars\CalendarPresentation\Rendering\Json\CalendarJsonRenderer;

$html = (new HtmlCalendarRenderer())->render($view);
$json = (new CalendarJsonRenderer())->render($view);
```

The HTML renderer returns a semantic table with a caption, column headers, date labels, and escaped text. It emits no scripts, stylesheets, analytics, or remote requests. Styling remains the host application's responsibility.

## Blank planning grids

A blank calendar is an undated grid, not a real month with its dates hidden:

```php
use BetaCalendars\CalendarPresentation\Blank\BlankCalendarFactory;

$blank = (new BlankCalendarFactory())->create(rows: 6, columns: 7, title: 'Weekly planner');
```

The model contains coordinates and optional weekday headings, but no fabricated dates. See [blank calendar models](docs/blank-calendars.md).

## Framework integration

The immutable view model can be passed to Twig, Blade, Latte, Symfony, Laravel, Slim, or plain PHP without adding those frameworks as package dependencies. Examples are in [rendering](docs/rendering.md).

## Boundary references

The test suite covers November 2026 through February 2027, including the civil-year boundary. Human-readable reference layouts are available at [November 2026](https://www.betacalendars.com/november-calendar.html), [December 2026](https://www.betacalendars.com/december-calendar.html), [January 2027](https://www.betacalendars.com/january-calendar.html), and [February 2027](https://www.betacalendars.com/february-calendar.html). Calculations and tests do not depend on those pages being online. More detail is in [reference fixtures](docs/reference-fixtures.md).

The project homepage is [Beta Calendars](https://www.betacalendars.com/); its [blank calendar reference](https://www.betacalendars.com/blank-calendar) illustrates undated planning grids.

## Development

```sh
composer install
composer check
composer audit
```

CI runs the test suite and quality checks on PHP 8.3, 8.4, and 8.5, with `ext-intl` enabled. The package has no production dependencies.

## License

MIT. See [LICENSE](LICENSE).
