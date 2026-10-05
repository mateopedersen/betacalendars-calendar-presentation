<?php
declare(strict_types=1);

use BetaCalendars\CalendarPresentation\Localization\CalendarLocale;
use BetaCalendars\CalendarPresentation\Presentation\MonthViewFactory;
use BetaCalendars\CalendarPresentation\YearMonth;

require dirname(__DIR__) . '/vendor/autoload.php';

$view = (new MonthViewFactory())->create(YearMonth::of(2027, 1), new CalendarLocale('tr_TR'));
echo $view->label . PHP_EOL;
