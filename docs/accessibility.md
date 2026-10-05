# Accessibility

The HTML renderer produces a table with a caption, a header row, `<th scope="col">` weekday headings, and date cells associated with those headings. Each visible date receives a localized accessible label. Outside-month cells also carry a stable class token; color alone should not communicate their state.

The renderer does not add keyboard behavior or ARIA grid roles. This package renders a static month view, not an interactive date picker. If an application adds interactive controls, it owns focus management, keyboard navigation, selection state, and the corresponding interaction semantics.

Current-date metadata is emitted only when the caller explicitly passes `currentDate`. It is exposed as a class/model flag; applications choose whether and how to announce it. Empty and hidden adjacent cells are marked `aria-hidden` and do not invent a date label.
