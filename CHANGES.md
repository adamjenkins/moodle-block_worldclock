# Changes

## v1.5

- Sorting rework: the site-wide "First timezone" setting is replaced by a
  per-instance ascending/descending sort order. Clocks are ordered
  chronologically by their current local date and time, with alphabetical
  tie-breaking for clocks sharing the same UTC offset.
- New per-instance "Show UTC offset" option (e.g. "UTC+9") next to each name.
- CI now tests Moodle 5.0, 5.1 and 5.2 with matching PHP versions
  (5.0: 8.2-8.3, 5.1: 8.2-8.4, 5.2: 8.3-8.4) on PostgreSQL and MariaDB.

## v1.4

- The day/night icon's boundary hours are configurable site-wide.

## v1.3

- "First timezone" ordering fixes; GitHub Actions CI added.

## v1.2

- "Show date > Only when different" sub-setting; day/night icon moved under
  the timezone name; optional waking-hours background colouring.

## v1.1

- Optional sun/moon day-night icon per timezone.

## v1.0

- Initial release: manual and automatic (course participants) timezone lists,
  live-updating clocks with DST support, per-instance display options.
