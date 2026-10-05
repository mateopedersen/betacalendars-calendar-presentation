<?php

declare(strict_types=1);

namespace BetaCalendars\CalendarPresentation\Rendering\Json;

use BetaCalendars\CalendarPresentation\Presentation\MonthView;
use JsonException;

final class CalendarJsonRenderer
{
    /** @throws JsonException */
    public function render(MonthView $view): string
    {
        return json_encode($view->toArray(), JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
