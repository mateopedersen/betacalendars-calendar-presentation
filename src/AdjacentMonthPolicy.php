<?php

declare(strict_types=1);

namespace BetaCalendars\CalendarPresentation;

enum AdjacentMonthPolicy
{
    case Show;
    case Hide;
    case Placeholder;
}
