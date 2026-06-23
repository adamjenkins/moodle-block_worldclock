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
 * Site administration settings for the World clock block.
 *
 * @package    block_worldclock
 * @copyright  2026 Adam Jenkins <adam@wisecat.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    $hourchoices = [];
    for ($hour = 0; $hour < 24; $hour++) {
        $hourchoices[$hour] = sprintf('%02d:00', $hour);
    }

    $settings->add(new admin_setting_configselect(
        'block_worldclock/nightstart',
        get_string('settingnightstart', 'block_worldclock'),
        get_string('settingnightstart_desc', 'block_worldclock'),
        23,
        $hourchoices
    ));

    $settings->add(new admin_setting_configselect(
        'block_worldclock/morningstart',
        get_string('settingmorningstart', 'block_worldclock'),
        get_string('settingmorningstart_desc', 'block_worldclock'),
        5,
        $hourchoices
    ));

    $settings->add(new admin_setting_configselect(
        'block_worldclock/daystart',
        get_string('settingdaystart', 'block_worldclock'),
        get_string('settingdaystart_desc', 'block_worldclock'),
        8,
        $hourchoices
    ));

    $settings->add(new admin_setting_configselect(
        'block_worldclock/eveningstart',
        get_string('settingeveningstart', 'block_worldclock'),
        get_string('settingeveningstart_desc', 'block_worldclock'),
        20,
        $hourchoices
    ));

    $settings->add(new admin_setting_configselect(
        'block_worldclock/referencetimezone',
        get_string('settingreferencetimezone', 'block_worldclock'),
        get_string('settingreferencetimezone_desc', 'block_worldclock'),
        'Pacific/Kiritimati',
        core_date::get_list_of_timezones(null, false)
    ));

    $settings->add(new admin_setting_configselect(
        'block_worldclock/icondaystart',
        get_string('settingicondaystart', 'block_worldclock'),
        get_string('settingicondaystart_desc', 'block_worldclock'),
        6,
        $hourchoices
    ));

    $settings->add(new admin_setting_configselect(
        'block_worldclock/iconnightstart',
        get_string('settingiconnightstart', 'block_worldclock'),
        get_string('settingiconnightstart_desc', 'block_worldclock'),
        18,
        $hourchoices
    ));
}
