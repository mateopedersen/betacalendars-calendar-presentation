<?php

declare(strict_types=1);

namespace BetaCalendars\CalendarPresentation\Model;

final readonly class GridCoordinate
{
    public function __construct(public int $row, public int $column)
    {
        if ($row < 0 || $column < 0 || $column > 6) {
            throw new \InvalidArgumentException('Grid coordinates must be non-negative and columns must be 0–6.');
        }
    }
}
