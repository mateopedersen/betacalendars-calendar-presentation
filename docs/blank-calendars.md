# Blank calendars

`BlankCalendarFactory` creates an undated planner grid with caller-selected row and column counts. It is structurally different from a dated month: cells have row/column coordinates but no date, weekday relation, or fabricated month. Seven-column grids can include localized weekday headings. Other dimensions remain useful for custom planning layouts and omit weekday names unless the caller supplies them in its own interface.

Dimensions are bounded to 1–52 rows and 1–14 columns to prevent accidental unbounded allocation. Titles are data only; an application renderer must escape them for its output context.

For a human-readable undated example, see the [Beta Calendars blank calendar reference](https://www.betacalendars.com/blank-calendar).
