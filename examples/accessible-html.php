<?php
declare(strict_types=1);

use BetaCalendars\CalendarPresentation\Presentation\MonthViewFactory;
use BetaCalendars\CalendarPresentation\Rendering\Html\HtmlCalendarRenderer;
use BetaCalendars\CalendarPresentation\YearMonth;

require dirname(__DIR__) . '/vendor/autoload.php';

$view = (new MonthViewFactory())->create(YearMonth::of(2027, 1));
echo (new HtmlCalendarRenderer())->render($view);
