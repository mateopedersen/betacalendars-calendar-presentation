<?php
declare(strict_types=1);

use BetaCalendars\CalendarPresentation\Presentation\MonthViewFactory;
use BetaCalendars\CalendarPresentation\YearMonth;

require dirname(__DIR__) . '/vendor/autoload.php';

$view = (new MonthViewFactory())->create(YearMonth::of(2027, 1));
echo $view->label . PHP_EOL;
