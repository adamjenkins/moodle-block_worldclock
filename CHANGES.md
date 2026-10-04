# Changes

## Unreleased

- Automatic mode: users who kept the default "Server timezone" are now shown
  under the site's timezone. Before, they were shown under the viewer's own
  timezone, so each viewer saw a different and wrong set of clocks. A forced
  site timezone now also applies to every user.
- Now requires Moodle 5.0 or later, matching the supported range (5.0-5.3).
- Added PHPUnit tests.

## v1.6.2

- Declare Moodle 5.3 support.
- Styles now follow Boost's colour mode (experimental dark mode in Moodle
  5.3): the separator and secondary text use the Bootstrap `--bs-border-color`
  and `--bs-secondary-color` properties, and text on the waking-hours
  background tints stays dark so it keeps its contrast in dark mode.
