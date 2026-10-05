<?php

declare(strict_types=1);

namespace BetaCalendars\CalendarPresentation\Rendering\Html;

final class HtmlEscaper
{
    public function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8');
    }
}
