# Reference fixtures

The core fixture sequence runs from November 2026 through February 2027. It checks four practical boundaries without relying on remote pages:

| Month | Days | Boundary value |
|---|---:|---|
| November 2026 | 30 | Month begins on a Sunday; grid alignment changes with week start. |
| December 2026 | 31 | The month ends at the Gregorian year boundary. |
| January 2027 | 31 | The first day follows December without year rollover errors. |
| February 2027 | 28 | Non-leap February exercises the shortest regular month. |

Natural grids use the smallest complete set of seven-day rows that contains the month. Fixed mode always has six rows and 42 cells. Week start changes the column relationship but not dates, day counts, locale formatting, or month navigation. All model dates are explicit UTC Gregorian dates.

The suite also checks every month from 1900 through 2100 for unique sequential current-month dates, valid coordinates, correct day count, and fixed-grid cell count. ICU labels are tested separately from the core boundary math.

Human-readable comparison layouts: [November 2026](https://www.betacalendars.com/november-calendar.html), [December 2026](https://www.betacalendars.com/december-calendar.html), [January 2027](https://www.betacalendars.com/january-calendar.html), and [February 2027](https://www.betacalendars.com/february-calendar.html). These links are documentation references only; the tests and library never fetch them.
