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
 * Form for editing world clock block instances.
 *
 * @package    block_worldclock
 * @copyright  2026 Adam Jenkins
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * World clock block instance config form.
 */
class block_worldclock_edit_form extends block_edit_form {
    /**
     * Define the block instance configuration form fields.
     *
     * @param MoodleQuickForm $mform
     */
    protected function specific_definition($mform) {
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        $mform->addElement('text', 'config_title', get_string('configtitle', 'block_worldclock'));
        $mform->setType('config_title', PARAM_TEXT);

        $modeoptions = [
            'manual' => get_string('configmodemanual', 'block_worldclock'),
            'auto' => get_string('configmodeauto', 'block_worldclock'),
        ];
        $mform->addElement('select', 'config_mode', get_string('configmode', 'block_worldclock'), $modeoptions);
        $mform->addHelpButton('config_mode', 'configmode', 'block_worldclock');
        $mform->setDefault('config_mode', 'manual');

        $mform->addElement('course', 'config_courseid', get_string('configcourseid', 'block_worldclock'), [
            'requiredcapabilities' => ['moodle/course:viewparticipants'],
        ]);
        $mform->addHelpButton('config_courseid', 'configcourseid', 'block_worldclock');
        $mform->hideIf('config_courseid', 'config_mode', 'eq', 'manual');

        $timezones = core_date::get_list_of_timezones(null, false);
        $select = $mform->addElement(
            'select',
            'config_timezones',
            get_string('configtimezones', 'block_worldclock'),
            $timezones
        );
        $select->setMultiple(true);
        $select->setSize(10);
        $mform->addHelpButton('config_timezones', 'configtimezones', 'block_worldclock');
        $mform->hideIf('config_timezones', 'config_mode', 'eq', 'auto');

        $mform->addElement('advcheckbox', 'config_format24', get_string('configformat24', 'block_worldclock'));
        $mform->setDefault('config_format24', 1);

        $mform->addElement('advcheckbox', 'config_showseconds', get_string('configshowseconds', 'block_worldclock'));
        $mform->setDefault('config_showseconds', 1);

        $mform->addElement('advcheckbox', 'config_showdate', get_string('configshowdate', 'block_worldclock'));
        $mform->setDefault('config_showdate', 0);
    }

    /**
     * Display the configuration form when the block is being added to the page.
     *
     * @return bool
     */
    public static function display_form_when_adding(): bool {
        return true;
    }
}
