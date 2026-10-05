<?php
declare(strict_types=1);


use BetaCalendars\CalendarPresentation\Blank\BlankCalendarFactory;
use BetaCalendars\CalendarPresentation\Localization\CalendarLocale;
use BetaCalendars\CalendarPresentation\Presentation\MonthViewFactory;
use BetaCalendars\CalendarPresentation\Rendering\Html\HtmlCalendarRenderer;
use BetaCalendars\CalendarPresentation\Rendering\Json\CalendarJsonRenderer;
use BetaCalendars\CalendarPresentation\WeekStart;
use BetaCalendars\CalendarPresentation\YearMonth;

require dirname(__DIR__) . '/vendor/autoload.php';

$destination = __DIR__ . '/snapshots';
if (!is_dir($destination) && !mkdir($destination, 0777, true) && !is_dir($destination)) {
    throw new RuntimeException('Could not create snapshot output directory.');
}

$factory = new MonthViewFactory();
$json = new CalendarJsonRenderer();
$fixtures = [
    'november-2026' => [YearMonth::of(2026, 11), new CalendarLocale('en_US')],
    'december-2026' => [YearMonth::of(2026, 12), new CalendarLocale('en_US')],
    'january-2027' => [YearMonth::of(2027, 1), new CalendarLocale('en_US')],
    'february-2027' => [YearMonth::of(2027, 2), new CalendarLocale('en_US')],
    'january-2027-tr' => [YearMonth::of(2027, 1), new CalendarLocale('tr_TR')],
    'february-2027-ja' => [YearMonth::of(2027, 2), new CalendarLocale('ja_JP')],
];
foreach ($fixtures as $name => [$month, $locale]) {
    file_put_contents($destination . '/' . $name . '.json', $json->render($factory->create($month, $locale)) . "\n");
}

$blank = (new BlankCalendarFactory())->create(rows: 6, columns: 7, weekStart: WeekStart::Monday);
file_put_contents(
    $destination . '/blank-6x7.json',
    json_encode($blank->toArray(), JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n",
);
file_put_contents(
    $destination . '/january-2027.html',
    (new HtmlCalendarRenderer())->render($factory->create(YearMonth::of(2027, 1))) . "\n",
);
