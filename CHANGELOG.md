# Changelog

All notable changes to the World clock block are documented in this file.

## [1.4] - 2026-06-23

- The day/night icon's boundary hours are now configurable site-wide
  ("Icon day start" / "Icon night start" in *Site administration > Plugins
  > Blocks > World clock*), instead of being fixed at 6am/6pm.

## [1.3.2] - 2026-06-23

- Fixed the "First timezone" ordering so it accounts for the calendar date,
  not just the hour of day: timezones whose offsets are a full day or more
  apart (e.g. UTC+14 and UTC-10, which share the same local hour but are a
  day apart) no longer collide in the sort order.

## [1.3.1] - 2026-06-23

- Replaced the distance-from-UTC ordering with a simpler "First timezone"
  setting: pick which timezone should appear first (defaults to
  Pacific/Kiritimati), and the rest are ordered by local time starting
  from there and working backwards around the clock.

## [1.3] - 2026-06-23

- Added a GitHub Actions CI workflow running the moodlehq/moodle-plugin-ci
  test suite against Moodle 5.0, 5.1, and 5.2 (PHP 8.2-8.4, matched to each
  branch's actual minimum PHP requirement).

## [1.2] - 2026-06-23

- "Show date" now has an "Only when different" sub-setting: when enabled,
  the date for a timezone is only shown if it differs from the date in the
  logged in user's own timezone; when disabled, the date is always shown.
- The day/night icon now renders underneath the timezone name instead of
  beside it.
- New per-instance "Colour background by time of day" option, colouring
  each timezone pink overnight, yellow during the morning/evening
  transition, and green during the day. The boundary hours are configurable
  in *Site administration > Plugins > Blocks > World clock*.

## [1.1] - 2026-06-23

- New per-instance option to show a sun or moon icon next to each timezone,
  depending on whether it is currently daytime (6am-6pm) or nighttime there.

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
