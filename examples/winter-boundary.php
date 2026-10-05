<?php
declare(strict_types=1);

use BetaCalendars\CalendarPresentation\Presentation\MonthViewFactory;
use BetaCalendars\CalendarPresentation\YearMonth;

require dirname(__DIR__) . '/vendor/autoload.php';

$factory = new MonthViewFactory();
foreach (['2026-11', '2026-12', '2027-01', '2027-02'] as $key) {
    $view = $factory->create(YearMonth::fromString($key));
    echo $view->label . ': ' . $view->rowCount() . ' rows' . PHP_EOL;
}
