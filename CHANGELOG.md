# Changelog

All notable changes to the World clock block are documented in this file.

## [1.0] - 2026-06-23

Initial release.

- World clock block, addable to course pages, activities, and the
  dashboard.
- Manual mode: display a fixed list of teacher/admin-selected timezones.
- Automatic mode: display every distinct timezone of users enrolled in a
  course, either the current course or an explicitly chosen course.
- Live-updating clocks (AMD module using `Intl.DateTimeFormat`), with
  DST handled correctly for named timezones.
- Per-instance options for 24-hour format, seconds, and date display.
- `null_provider` privacy implementation.
