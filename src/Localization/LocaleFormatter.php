<?php

declare(strict_types=1);

namespace BetaCalendars\CalendarPresentation\Localization;

use DateTimeImmutable;
use DateTimeZone;

final class LocaleFormatter
{
    private const MONTHS = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December',
    ];
    private const WEEKDAYS = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

    public function format(DateTimeImmutable $date, string $locale, string $pattern): string
    {
        if (class_exists(\IntlDateFormatter::class)) {
            $formatter = \IntlDateFormatter::create(
                $locale,
                \IntlDateFormatter::NONE,
                \IntlDateFormatter::NONE,
                'UTC',
                \IntlDateFormatter::GREGORIAN,
                $pattern,
            );
            $value = $formatter?->format($date);
            if (!is_string($value)) {
                throw new InvalidLocaleException(sprintf('ICU could not format locale "%s".', $locale));
            }
            return $value;
        }

        if (preg_match('/^en(?:[_-]|$)/i', $locale) !== 1) {
            throw new InvalidLocaleException(sprintf('Locale "%s" requires the optional ext-intl extension.', $locale));
        }

        $utcDate = $date->setTimezone(new DateTimeZone('UTC'));
        return match ($pattern) {
            'MMMM y' => self::MONTHS[(int) $utcDate->format('n') - 1] . ' ' . $utcDate->format('Y'),
            'MMM y' => substr(self::MONTHS[(int) $utcDate->format('n') - 1], 0, 3) . ' ' . $utcDate->format('Y'),
            'EEEE' => self::WEEKDAYS[((int) $utcDate->format('N') - 1) % 7],
            'EEE' => substr(self::WEEKDAYS[((int) $utcDate->format('N') - 1) % 7], 0, 3),
            'EEEEE' => substr(self::WEEKDAYS[((int) $utcDate->format('N') - 1) % 7], 0, 1),
            'EEEE, MMMM d, y' => self::WEEKDAYS[((int) $utcDate->format('N') - 1) % 7] . ', '
                . self::MONTHS[(int) $utcDate->format('n') - 1] . ' ' . $utcDate->format('j, Y'),
            default => throw new InvalidLocaleException(sprintf('Unsupported fallback date pattern "%s".', $pattern)),
        };
    }
}
