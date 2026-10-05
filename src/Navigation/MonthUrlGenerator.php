<?php

declare(strict_types=1);

namespace BetaCalendars\CalendarPresentation\Navigation;

use BetaCalendars\CalendarPresentation\YearMonth;

interface MonthUrlGenerator
{
    public function generate(YearMonth $month): string;
}
