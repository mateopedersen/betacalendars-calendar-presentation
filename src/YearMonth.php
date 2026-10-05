<?php
declare(strict_types=1);

namespace BetaCalendars\CalendarPresentation;

use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;

final readonly class YearMonth
{
    private function __construct(public int $year, public int $month)
    {
        if ($year < 1 || $year > 9999 || $month < 1 || $month > 12) {
            throw new InvalidArgumentException('Year must be 1–9999 and month must be 1–12.');
        }
    }

    public static function of(int $year, int $month): self
    {
        return new self($year, $month);
    }

    public static function fromString(string $value): self
    {
        if (preg_match('/^(\d{4})-(0[1-9]|1[0-2])$/D', $value, $matches) !== 1) {
            throw new InvalidArgumentException('Month must use the YYYY-MM format.');
        }
        return new self((int) $matches[1], (int) $matches[2]);
    }

    public function previous(): self { return $this->plusMonths(-1); }
    public function next(): self { return $this->plusMonths(1); }

    public function plusMonths(int $months): self
    {
        $index = ($this->year - 1) * 12 + ($this->month - 1) + $months;
        if ($months < -((($this->year - 1) * 12) + ($this->month - 1))
            || $months > ((9999 * 12) - 1) - ((($this->year - 1) * 12) + ($this->month - 1))) {
            throw new InvalidArgumentException('Month arithmetic exceeded the supported year range.');
        }
        return new self(intdiv($index, 12) + 1, ($index % 12) + 1);
    }

    public function daysInMonth(): int
    {
        return (int) $this->firstDate()->format('t');
    }

    public function firstDate(): DateTimeImmutable
    {
        return new DateTimeImmutable(sprintf('%04d-%02d-01 00:00:00', $this->year, $this->month), new DateTimeZone('UTC'));
    }

    public function lastDate(): DateTimeImmutable
    {
        return $this->firstDate()->modify('last day of this month');
    }

    public function iso(): string { return sprintf('%04d-%02d', $this->year, $this->month); }
}
