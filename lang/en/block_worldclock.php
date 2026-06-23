<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Strings for component 'block_worldclock'.
 *
 * @package    block_worldclock
 * @copyright  2026 Adam Jenkins <adam@wisecat.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['configcourseid'] = 'Course';
$string['configcourseid_help'] = 'The course whose enrolled users\' timezones should be displayed. If left blank, the course the block is currently displayed on is used (this only works when the block is added to a course page, not the dashboard).';
$string['configformat24'] = 'Use 24-hour time format';
$string['configmode'] = 'Timezones to display';
$string['configmode_help'] = 'Choose whether to display a fixed list of timezones that you select, or to automatically display every distinct timezone used by users enrolled in a course.';
$string['configmodeauto'] = 'All timezones of users enrolled in a course';
$string['configmodemanual'] = 'Selected timezones';
$string['configshowdate'] = 'Show date';
$string['configshowdateonlydiff'] = 'Only when different';
$string['configshowdateonlydiff_help'] = 'Only show the date for a timezone when it differs from the date in the logged in user\'s own timezone. When unticked, the date is always shown for every timezone.';
$string['configshowicon'] = 'Show day/night icon';
$string['configshowicon_help'] = 'Display a sun or moon icon underneath each timezone\'s name, depending on whether it is currently daytime or nighttime there. The day/night boundary hours can be changed in the plugin\'s site administration settings.';
$string['configshowseconds'] = 'Show seconds';
$string['configtimezones'] = 'Timezones';
$string['configtimezones_help'] = 'Hold Ctrl (or Cmd on Mac) to select multiple timezones. Only used when "Selected timezones" mode is chosen above.';
$string['configtitle'] = 'Block title';
$string['configwakinghoursbg'] = 'Colour background by time of day';
$string['configwakinghoursbg_help'] = 'Colour each timezone\'s background according to the time of day there: pink overnight, yellow during the morning and evening transition, and green during the day. The boundary times can be changed in the plugin\'s site administration settings.';
$string['daytime'] = 'Daytime';
$string['nighttime'] = 'Nighttime';
$string['notimezonesconfigured'] = 'No timezones are configured for this block yet. Edit the block settings to choose timezones to display.';
$string['pluginname'] = 'World clock';
$string['privacy:metadata'] = 'The World clock block only stores configuration set by the person who added the block (a title and a list of timezones). In automatic mode it reads, but never stores, the timezone of enrolled users in order to render the block.';
$string['settingdaystart'] = 'Day start';
$string['settingdaystart_desc'] = 'The hour at which the daytime (green) background period starts.';
$string['settingeveningstart'] = 'Evening start';
$string['settingeveningstart_desc'] = 'The hour at which the evening (yellow) background period starts.';
$string['settingicondaystart'] = 'Icon day start';
$string['settingicondaystart_desc'] = 'The hour at which the day/night icon switches to the sun (daytime) icon.';
$string['settingiconnightstart'] = 'Icon night start';
$string['settingiconnightstart_desc'] = 'The hour at which the day/night icon switches to the moon (nighttime) icon.';
$string['settingmorningstart'] = 'Morning start';
$string['settingmorningstart_desc'] = 'The hour at which the morning (yellow) background period starts.';
$string['settingnightstart'] = 'Night start';
$string['settingnightstart_desc'] = 'The hour at which the overnight (pink) background period starts.';
$string['settingreferencetimezone'] = 'First timezone';
$string['settingreferencetimezone_desc'] = 'The timezone that should appear first in every block. The remaining timezones are ordered by local time, starting from this timezone and working backwards around the clock.';
$string['worldclock:addinstance'] = 'Add a new world clock block';
$string['worldclock:myaddinstance'] = 'Add a new world clock block to the My Moodle page';
