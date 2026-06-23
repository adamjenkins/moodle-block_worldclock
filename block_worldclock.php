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
 * World clock block.
 *
 * @package    block_worldclock
 * @copyright  2026 Adam Jenkins
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * World clock block class.
 */
class block_worldclock extends block_base {
    /** Maximum number of distinct timezones to render in auto mode. */
    const MAX_AUTO_ZONES = 12;

    /**
     * Set the block title.
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_worldclock');
    }

    /**
     * This block does not have site-wide settings.
     *
     * @return bool
     */
    public function has_config() {
        return false;
    }

    /**
     * Defines where this block can be added.
     *
     * @return array
     */
    public function applicable_formats() {
        return ['course-view' => true, 'mod' => true, 'my' => true];
    }

    /**
     * Allow multiple instances of this block per page.
     *
     * @return bool
     */
    public function instance_allow_multiple() {
        return true;
    }

    /**
     * Set the block title from instance config, if set.
     */
    public function specialization() {
        if (!empty($this->config->title)) {
            $this->title = format_string($this->config->title, true, ['context' => $this->context]);
        } else {
            $this->title = get_string('pluginname', 'block_worldclock');
        }
    }

    /**
     * Build the block content.
     *
     * @return stdClass
     */
    public function get_content() {
        global $OUTPUT;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->footer = '';

        $mode = $this->config->mode ?? 'manual';

        if ($mode === 'auto') {
            $clocks = $this->get_clocks_for_course_users();
        } else {
            $clocks = $this->get_clocks_for_selected_timezones();
        }

        if (empty($clocks)) {
            $this->content->text = $OUTPUT->notification(
                get_string('notimezonesconfigured', 'block_worldclock'),
                'info',
                false
            );
            return $this->content;
        }

        $format24 = !empty($this->config->format24);
        $showseconds = $this->config->showseconds ?? true;
        $showdate = !empty($this->config->showdate);

        $uniqid = 'block_worldclock_' . $this->instance->id;

        $this->content->text = $OUTPUT->render_from_template('block_worldclock/clock', [
            'uniqid' => $uniqid,
            'clocks' => $clocks,
        ]);

        $this->page->requires->js_call_amd('block_worldclock/clock', 'init', [
            $uniqid,
            (bool) $format24,
            (bool) $showseconds,
            (bool) $showdate,
        ]);

        return $this->content;
    }

    /**
     * Build the clock list from the manually configured timezone list.
     *
     * @return array
     */
    protected function get_clocks_for_selected_timezones(): array {
        $selected = $this->config->timezones ?? [];
        if (empty($selected)) {
            return [];
        }

        $alltimezones = core_date::get_list_of_timezones(null, false);

        $clocks = [];
        foreach ($selected as $tz) {
            $clocks[] = [
                'timezone' => $tz,
                'utcoffset' => '',
                'label' => $alltimezones[$tz] ?? $tz,
            ];
        }

        return $clocks;
    }

    /**
     * Build the clock list from the distinct timezones of users enrolled in a course.
     *
     * Uses the course configured for this block instance if set, otherwise falls
     * back to the course the block is currently being displayed on.
     *
     * @return array
     */
    protected function get_clocks_for_course_users(): array {
        $courseid = $this->config->courseid ?? 0;
        if (empty($courseid)) {
            $course = $this->page->course;
            if (empty($course) || $course->id == SITEID) {
                return [];
            }
            $courseid = $course->id;
        }

        try {
            $coursecontext = context_course::instance($courseid);
        } catch (Exception $e) {
            return [];
        }

        if (!has_capability('moodle/course:viewparticipants', $coursecontext)) {
            return [];
        }

        $fields = 'u.id, u.timezone';
        $users = get_enrolled_users($coursecontext, '', 0, $fields, null, 0, 0, true);

        $alltimezones = core_date::get_list_of_timezones(null, false);

        $seen = [];
        foreach ($users as $user) {
            $resolved = get_user_timezone($user->timezone);

            if (is_numeric($resolved)) {
                $key = 'offset:' . $resolved;
                if (!isset($seen[$key])) {
                    $seen[$key] = [
                        'timezone' => '',
                        'utcoffset' => (string) $resolved,
                        'label' => $this->get_offset_label((float) $resolved),
                    ];
                }
            } else {
                $key = 'tz:' . $resolved;
                if (!isset($seen[$key])) {
                    $seen[$key] = [
                        'timezone' => $resolved,
                        'utcoffset' => '',
                        'label' => $alltimezones[$resolved] ?? $resolved,
                    ];
                }
            }
        }

        $clocks = array_values($seen);

        usort($clocks, function ($a, $b) {
            return strcmp($a['label'], $b['label']);
        });

        return array_slice($clocks, 0, self::MAX_AUTO_ZONES);
    }

    /**
     * Build a human readable label for a fixed UTC offset.
     *
     * @param float $offset
     * @return string
     */
    protected function get_offset_label(float $offset): string {
        if ($offset == 0) {
            return 'UTC';
        }
        $sign = $offset > 0 ? '+' : '-';
        return 'UTC' . $sign . number_format(abs($offset), 1);
    }
}
