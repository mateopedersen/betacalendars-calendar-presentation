# Architecture

The library separates civil-calendar math from presentation and output:

1. `YearMonth` holds an explicit Gregorian year and month.
2. `CalendarMonthFactory` maps real dates to an immutable week/day grid.
3. `MonthViewFactory` adds locale labels, navigation, relation, weekend, and optional current-date metadata.
4. HTML and JSON renderers serialize that presentation contract for applications.

Blank planning grids use a separate model because an undated cell has no civil date or month relation. Navigation can be bounded, while URL construction is left to an application-provided `MonthUrlGenerator`.

There is no global locale, implicit clock, mutable date object, framework dependency, or network client. The production package has no dependencies; ICU support is discovered at runtime and is optional.
