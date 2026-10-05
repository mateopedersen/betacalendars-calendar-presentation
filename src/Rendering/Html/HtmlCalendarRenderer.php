<?php
declare(strict_types=1);

namespace BetaCalendars\CalendarPresentation\Rendering\Html;

use BetaCalendars\CalendarPresentation\Presentation\MonthView;

final class HtmlCalendarRenderer
{
    public function __construct(private HtmlEscaper $escaper = new HtmlEscaper()) {}

    public function render(MonthView $view, HtmlRenderOptions $options = new HtmlRenderOptions()): string
    {
        $prefix = $this->escaper->escape($options->classPrefix);
        $caption = $this->escaper->escape($options->caption ?? $view->label);
        $aria = $this->escaper->escape($view->ariaLabel);
        $html = '<table class="' . $prefix . '" aria-label="' . $aria . '"><caption class="' . $prefix . '__caption">' . $caption . '</caption>';
        $html .= '<thead><tr class="' . $prefix . '__weekdays">';
        foreach ($view->weekdayHeaders as $column => $header) {
            $id = $prefix . '-weekday-' . $column;
            $html .= '<th id="' . $this->escaper->escape($id) . '" scope="col" abbr="'
                . $this->escaper->escape($header['label']) . '">' . $this->escaper->escape($header['narrow']) . '</th>';
        }
        $html .= '</tr></thead><tbody>';
        foreach ($view->weeks as $week) {
            $html .= '<tr class="' . $prefix . '__week">';
            foreach ($week->days as $day) {
                $tokens = array_map(
                    fn (string $token): string => $this->escaper->escape(str_replace('calendar', $prefix, $token)),
                    $day->cssTokens,
                );
                $class = $this->escaper->escape(implode(' ', $tokens));
                if ($day->isoDate === null) {
                    $html .= '<td class="' . $class . '" aria-hidden="true"></td>';
                    continue;
                }
                $html .= '<td class="' . $class . '" headers="' . $prefix . '-weekday-' . $day->column
                    . '" data-date="' . $this->escaper->escape($day->isoDate) . '" aria-label="'
                    . $this->escaper->escape($day->ariaLabel ?? '') . '">' . $day->dayNumber . '</td>';
            }
            $html .= '</tr>';
        }
        return $html . '</tbody></table>';
    }
}
