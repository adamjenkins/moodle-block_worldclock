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
 * @copyright  2026 Adam Jenkins <adam@wisecat.net>
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
     * This block has site-wide settings for the waking-hours background boundaries.
     *
     * @return bool
     */
    public function has_config() {
        return true;
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
        global $OUTPUT, $USER;

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

        $clocks = $this->sort_clocks_by_reference_timezone($clocks);

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
        $showdateonlydiff = !empty($this->config->showdateonlydiff);
        $showicon = !empty($this->config->showicon);
        $wakinghoursbg = !empty($this->config->wakinghoursbg);

        $uniqid = 'block_worldclock_' . $this->instance->id;

        $userresolved = get_user_timezone($USER->timezone ?? '');
        $usertimezone = is_numeric($userresolved) ? '' : $userresolved;
        $userutcoffset = is_numeric($userresolved) ? (string) $userresolved : '';

        $this->content->text = $OUTPUT->render_from_template('block_worldclock/clock', [
            'uniqid' => $uniqid,
            'clocks' => $clocks,
            'usertimezone' => $usertimezone,
            'userutcoffset' => $userutcoffset,
        ]);

        $this->page->requires->js_call_amd('block_worldclock/clock', 'init', [
            $uniqid,
            [
                'format24' => (bool) $format24,
                'showSeconds' => (bool) $showseconds,
                'showDate' => (bool) $showdate,
                'showDateOnlyDiff' => (bool) $showdateonlydiff,
                'showIcon' => (bool) $showicon,
                'daytimeLabel' => get_string('daytime', 'block_worldclock'),
                'nighttimeLabel' => get_string('nighttime', 'block_worldclock'),
                'iconDayStart' => $this->get_hour_setting('icondaystart', 6),
                'iconNightStart' => $this->get_hour_setting('iconnightstart', 18),
                'wakingHoursBg' => (bool) $wakinghoursbg,
                'nightStart' => $this->get_hour_setting('nightstart', 23),
                'morningStart' => $this->get_hour_setting('morningstart', 5),
                'dayStart' => $this->get_hour_setting('daystart', 8),
                'eveningStart' => $this->get_hour_setting('eveningstart', 20),
            ],
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

        return array_slice(array_values($seen), 0, self::MAX_AUTO_ZONES);
    }

    /**
     * Sort clocks by local date and time, starting from the 'referencetimezone' admin
     * setting (Pacific/Kiritimati by default) and working backwards around the clock.
     *
     * Clocks are ordered by their real UTC offset, not by hour-of-day alone, so that
     * timezones whose offsets differ by a full day or more (e.g. UTC+14 and UTC-10,
     * which read the same local hour but are a calendar day apart) are placed
     * correctly relative to each other instead of colliding.
     *
     * @param array $clocks
     * @return array
     */
    protected function sort_clocks_by_reference_timezone(array $clocks): array {
        $referenceclock = ['timezone' => $this->get_reference_timezone_setting(), 'utcoffset' => ''];
        $referenceoffset = $this->get_clock_utc_offset_hours($referenceclock);

        $decorated = array_map(function ($clock) {
            return ['offset' => $this->get_clock_utc_offset_hours($clock), 'clock' => $clock];
        }, $clocks);

        usort($decorated, function ($a, $b) {
            return $b['offset'] <=> $a['offset'];
        });

        // Clocks at or behind the reference keep their place; clocks ahead of the
        // reference are a calendar day further on, so they wrap around to the end.
        $before = [];
        $after = [];
        foreach ($decorated as $entry) {
            if ($entry['offset'] <= $referenceoffset) {
                $before[] = $entry['clock'];
            } else {
                $after[] = $entry['clock'];
            }
        }

        return array_merge($before, $after);
    }

    /**
     * Get the current UTC offset, in hours, for a clock entry.
     *
     * @param array $clock {timezone, utcoffset, label}
     * @return float
     */
    protected function get_clock_utc_offset_hours(array $clock): float {
        if ($clock['utcoffset'] !== '') {
            return (float) $clock['utcoffset'];
        }

        try {
            $timezone = new DateTimeZone($clock['timezone']);
            $datetime = new DateTime('now', $timezone);
            return $timezone->getOffset($datetime) / 3600;
        } catch (Exception $e) {
            return 0.0;
        }
    }

    /**
     * Read the 'referencetimezone' admin setting.
     *
     * @return string
     */
    protected function get_reference_timezone_setting(): string {
        $value = get_config('block_worldclock', 'referencetimezone');
        return empty($value) ? 'Pacific/Kiritimati' : (string) $value;
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

    /**
     * Read an hour-of-day admin setting, falling back to a default if unset.
     *
     * @param string $name
     * @param int $default
     * @return int
     */
    protected function get_hour_setting(string $name, int $default): int {
        $value = get_config('block_worldclock', $name);
        return $value === false ? $default : (int) $value;
    }
}
