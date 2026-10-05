<?php

declare(strict_types=1);

namespace BetaCalendars\CalendarPresentation;

enum MonthRelation
{
    case Previous;
    case Current;
    case Next;
}
