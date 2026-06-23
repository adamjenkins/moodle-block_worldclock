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
- Per-instance display options: 24-hour vs 12-hour format, seconds, and date.
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
- **Use 24-hour time format**, **Show seconds**, **Show date** — display
  options.

In automatic mode, the viewing user must have the
`moodle/course:viewparticipants` capability in the source course or the
block will show nothing.

## Privacy

The block only stores the configuration chosen by whoever added it (a
title, a mode, and a list of timezones or a course id). In automatic mode
it reads, but never stores, the timezone field of enrolled users purely to
render the block for the current request. See `classes/privacy/provider.php`.

## Support

Report issues to the plugin maintainer.
