# Changes

## v1.6.3

- Automatic mode: users who kept the default "Server timezone" are now shown
  under the site's timezone. Before, they were shown under the viewer's own
  timezone, so each viewer saw a different and wrong set of clocks. A forced
  site timezone now also applies to every user.
- Now requires Moodle 5.0 or later, matching the supported range (5.0-5.3).
- Added PHPUnit tests.
- Installing with Composer no longer caps the Moodle version: `composer.json`
  now requires `moodle/moodle` `^5.0` (was `>=5.0 <5.4`).
- Continuous integration now tests against the released Moodle 5.3
  (`MOODLE_503_STABLE`) instead of Moodle's development branch.
- Pushing a release tag now also publishes the release to the camp registry.
