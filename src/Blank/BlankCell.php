<?php

declare(strict_types=1);

namespace BetaCalendars\CalendarPresentation\Blank;

final readonly class BlankCell
{
    public function __construct(public int $row, public int $column)
    {
        if ($row < 0 || $column < 0) {
            throw new \InvalidArgumentException('Blank cell coordinates must be non-negative.');
        }
    }
}
