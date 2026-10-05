<?php
declare(strict_types=1);

namespace BetaCalendars\CalendarPresentation\Navigation;

use BetaCalendars\CalendarPresentation\YearMonth;

final readonly class MonthNavigation
{
    private function __construct(private YearMonth $current, private ?YearMonth $minimum, private ?YearMonth $maximum) {}

    public static function for(YearMonth $current, ?YearMonth $minimum = null, ?YearMonth $maximum = null): self
    {
        if ($minimum !== null && $maximum !== null && $minimum->iso() > $maximum->iso()) {
            throw new \InvalidArgumentException('Minimum month must not be after maximum month.');
        }
        if (($minimum !== null && $current->iso() < $minimum->iso()) || ($maximum !== null && $current->iso() > $maximum->iso())) {
            throw new \InvalidArgumentException('Current month must be inside the configured navigation range.');
        }
        return new self($current, $minimum, $maximum);
    }

    public function current(): YearMonth { return $this->current; }

    public function previous(): ?YearMonth
    {
        try { $candidate = $this->current->previous(); } catch (\InvalidArgumentException) { return null; }
        return $this->minimum !== null && $candidate->iso() < $this->minimum->iso() ? null : $candidate;
    }

    public function next(): ?YearMonth
    {
        try { $candidate = $this->current->next(); } catch (\InvalidArgumentException) { return null; }
        return $this->maximum !== null && $candidate->iso() > $this->maximum->iso() ? null : $candidate;
    }
}
