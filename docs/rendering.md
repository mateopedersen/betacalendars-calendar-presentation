# Rendering and framework use

`MonthView` is a framework-neutral immutable model. Templates may read its properties directly; no Twig, Blade, Latte, Laravel, or Symfony packages are required.

## Twig

```php
$view = $factory->create(YearMonth::of(2027, 1));
return $twig->render('calendar.html.twig', ['calendar' => $view]);
```

## Laravel / Blade

```php
return view('calendar', ['calendar' => $factory->create(YearMonth::of(2027, 1))]);
```

## Symfony controller

```php
return $this->render('calendar/month.html.twig', [
    'calendar' => $factory->create(YearMonth::of(2027, 1)),
]);
```

## Plain PHP

Use `HtmlCalendarRenderer` when a complete static table is useful, or pass the view object to an application template. The renderer escapes captions and labels, returns markup only, and does not include styles or scripts. `CalendarJsonRenderer` emits a deterministic JSON shape with `JSON_THROW_ON_ERROR`; exceptions are allowed to reach the caller.

If an application needs URLs, implement `MonthUrlGenerator` for its own route structure. The library does not assume a website route.
