<?php
declare(strict_types=1);

use BetaCalendars\CalendarPresentation\Blank\BlankCalendarFactory;
use BetaCalendars\CalendarPresentation\WeekStart;

require dirname(__DIR__) . '/vendor/autoload.php';

$blank = (new BlankCalendarFactory())->create(rows: 6, columns: 7, weekStart: WeekStart::Monday, title: 'Weekly planner');
echo json_encode($blank->toArray(), JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR) . PHP_EOL;
