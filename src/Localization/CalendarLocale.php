<?php

declare(strict_types=1);

namespace BetaCalendars\CalendarPresentation\Localization;

use BetaCalendars\CalendarPresentation\WeekStart;

final readonly class CalendarLocale
{
    public function __construct(public string $id, public WeekStart $weekStart = WeekStart::Monday)
    {
        if ($id === '' || preg_match('/^[A-Za-z]{2,3}(?:[_-][A-Za-z0-9]{2,8})*$/D', $id) !== 1) {
            throw new InvalidLocaleException('Locale must be a language tag such as en_US or tr_TR.');
        }
    }
}
