# Localization

Pass a locale explicitly with `CalendarLocale`, for example `en_US`, `tr_TR`, `de_DE`, `fr_FR`, or `ja_JP`. When PHP's `ext-intl` is installed, labels are formatted through ICU using UTC Gregorian dates. Week start is an explicit input and does not silently change with the locale; this keeps layout policy under the application's control.

`ext-intl` is optional. Without it, English labels are available. Requests for other locales throw `InvalidLocaleException` instead of returning a falsely localized result. ICU versions can legitimately vary in punctuation and spacing, so applications should avoid treating every formatted string as a universal byte-for-byte constant.

The package does not infer the locale from process configuration, browser headers, or the current date. Accessibility labels use the same locale formatter as visible labels.
