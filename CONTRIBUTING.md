# Contributing

Issues and pull requests are welcome. Please include a focused description of the behavior being changed and add tests for changes to date calculations, locale handling, or rendering.

## Local checks

Use PHP 8.3 or newer and Composer:

```sh
composer install
composer check
composer audit
```

The test matrix enables `ext-intl`; local runs without it skip ICU-specific language assertions and exercise the documented English fallback. Production code must remain framework independent and deterministic. Do not add network calls to the test suite.
