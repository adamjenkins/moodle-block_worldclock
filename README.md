# World clock block

A Moodle block that displays the current time in multiple timezones. It can
be added to course pages, activities, and the Moodle dashboard.

## Features

- **Manual mode** — pick any timezones from the full IANA timezone list to
  display, per block instance.
- **Automatic mode** — instead of a fixed list, the block displays every
  distinct timezone used by users enrolled in a course. On a course page
  this defaults to that course; anywhere else (e.g. the dashboard) you can
  explicitly choose the source course.
- Clocks update live in the browser (no page refresh) and correctly handle
  daylight saving time for named timezones.
- "Show date" can be restricted to "Only when different", so a timezone's
  date only appears when it differs from the viewing user's own date.
- Per-instance display options: 24-hour vs 12-hour format, seconds, date,
  a sun/moon day-night icon underneath each timezone's name (day/night
  boundary hours configurable site-wide), and a waking-hours background
  colour (pink overnight, yellow at the morning/evening transition, green
  during the day — boundary hours also configurable site-wide).
- Timezones are ordered by local time, starting from a site-wide "First
  timezone" setting (Pacific/Kiritimati by default) and working backwards
  around the clock.
- Multiple instances of the block can be added to the same page.

## Installation

Copy (or check out) this plugin into `blocks/worldclock` in your Moodle
installation, then visit *Site administration > Notifications* to complete
the install.

## Configuration

Add the block to a course page or the dashboard, then use the block's
*Configure* action to choose:

- **Block title** — optional override of the default title.
- **Timezones to display** — *Selected timezones* (manual list) or
  *All timezones of users enrolled in a course* (automatic).
- **Course** — only used in automatic mode; the course whose enrolled
  users' timezones should be shown. Leave blank to use the course the block
  is currently displayed on.
- **Timezones** — only used in manual mode; the fixed list of timezones to
  show.
- **Use 24-hour time format**, **Show seconds**, **Show date** (with an
  **Only when different** sub-setting), **Show day/night icon**,
  **Colour background by time of day** — display options.

In automatic mode, the viewing user must have the
`moodle/course:viewparticipants` capability in the source course or the
block will show nothing.

### Site administration settings

Under *Site administration > Plugins > Blocks > World clock*:

- The waking-hours background boundary hours (night/morning/day/evening
  start) apply to every block instance that has **Colour background by
  time of day** enabled.
- **First timezone** controls which timezone appears first in every block
  instance (defaults to Pacific/Kiritimati); the rest follow in order of
  local time, working backwards around the clock from there.
- **Icon day start** / **Icon night start** control the boundary hours for
  the sun/moon day-night icon (defaults to 6am/6pm), for every block
  instance that has **Show day/night icon** enabled.

## Continuous integration

A GitHub Actions workflow (`.github/workflows/ci.yml`) runs the
[moodle-plugin-ci](https://github.com/moodlehq/moodle-plugin-ci) test suite
against Moodle 5.0, 5.1, and 5.2, using every PHP version each branch
actually supports (5.0/5.1: PHP 8.2-8.4; 5.2: PHP 8.3-8.4, since Moodle 5.2
raises its minimum PHP requirement to 8.3), crossed with both Postgres and
MariaDB.

## Privacy

The block only stores the configuration chosen by whoever added it (a
title, a mode, and a list of timezones or a course id). In automatic mode
it reads, but never stores, the timezone field of enrolled users purely to
render the block for the current request. See `classes/privacy/provider.php`.

## Support

Report issues to the plugin maintainer.
