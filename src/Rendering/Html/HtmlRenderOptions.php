<?php

declare(strict_types=1);

namespace BetaCalendars\CalendarPresentation\Rendering\Html;

use InvalidArgumentException;

final readonly class HtmlRenderOptions
{
    public function __construct(public ?string $caption = null, public string $classPrefix = 'calendar')
    {
        if ($classPrefix === '' || preg_match('/^[A-Za-z][A-Za-z0-9_-]*$/D', $classPrefix) !== 1) {
            throw new InvalidArgumentException('CSS class prefix must be a simple identifier.');
        }
    }
}
