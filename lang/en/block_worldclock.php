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
 * @copyright  2026 Adam Jenkins
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
$string['configshowseconds'] = 'Show seconds';
$string['configtimezones'] = 'Timezones';
$string['configtimezones_help'] = 'Hold Ctrl (or Cmd on Mac) to select multiple timezones. Only used when "Selected timezones" mode is chosen above.';
$string['configtitle'] = 'Block title';
$string['notimezonesconfigured'] = 'No timezones are configured for this block yet. Edit the block settings to choose timezones to display.';
$string['pluginname'] = 'World clock';
$string['privacy:metadata'] = 'The World clock block only stores configuration set by the person who added the block (a title and a list of timezones). In automatic mode it reads, but never stores, the timezone of enrolled users in order to render the block.';
$string['worldclock:addinstance'] = 'Add a new world clock block';
$string['worldclock:myaddinstance'] = 'Add a new world clock block to the My Moodle page';
