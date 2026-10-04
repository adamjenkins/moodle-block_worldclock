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

namespace block_worldclock;

use block_worldclock;
use moodle_page;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/blocks/moodleblock.class.php');
require_once($CFG->dirroot . '/blocks/worldclock/block_worldclock.php');

/**
 * Tests for the world clock block.
 *
 * @package    block_worldclock
 * @copyright  2026 Adam Jenkins <adam@wisecat.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(block_worldclock::class)]
final class block_worldclock_test extends \advanced_testcase {
    /**
     * Build a block instance with the given instance config on the given course page.
     *
     * @param array $config
     * @param \stdClass|null $course
     * @return block_worldclock
     */
    private function make_block(array $config, ?\stdClass $course = null): block_worldclock {
        $page = new moodle_page();
        if ($course) {
            $page->set_course($course);
            $page->set_context(\context_course::instance($course->id));
        } else {
            $page->set_context(\context_system::instance());
        }
        $page->set_url('/');

        $block = new block_worldclock();
        $block->page = $page;
        $block->config = (object) $config;
        $block->instance = (object) ['id' => 42];
        return $block;
    }

    /**
     * Call a protected method of the block.
     *
     * @param block_worldclock $block
     * @param string $method
     * @param array $args
     * @return mixed
     */
    private function call(block_worldclock $block, string $method, array $args = []) {
        $reflection = new \ReflectionMethod($block, $method);
        return $reflection->invokeArgs($block, $args);
    }

    /**
     * Timezone names of a clock list, sorted.
     *
     * @param array $clocks
     * @return string[]
     */
    private function zones(array $clocks): array {
        $zones = array_map(
            fn($clock) => $clock['timezone'] !== '' ? $clock['timezone'] : 'offset:' . $clock['utcoffset'],
            $clocks
        );
        sort($zones);
        return $zones;
    }

    /**
     * Users on the '99' (server default) sentinel resolve to the server timezone, not the viewer's.
     */
    public function test_auto_mode_default_timezone_resolves_to_server_timezone(): void {
        global $USER;
        $this->resetAfterTest();
        $gen = $this->getDataGenerator();

        set_config('timezone', 'Europe/London');
        set_config('forcetimezone', 99);

        $course = $gen->create_course();
        $default = $gen->create_user(['timezone' => '99']);
        $named = $gen->create_user(['timezone' => 'America/New_York']);
        $gen->enrol_user($default->id, $course->id, 'student');
        $gen->enrol_user($named->id, $course->id, 'student');

        // The viewer (not enrolled) is in a third zone.
        $this->setAdminUser();
        $USER->timezone = 'Asia/Tokyo';

        $block = $this->make_block(['mode' => 'auto', 'courseid' => $course->id]);
        $clocks = $this->call($block, 'get_clocks_for_course_users');

        $this->assertSame(['America/New_York', 'Europe/London'], $this->zones($clocks));
    }

    /**
     * A forced site timezone overrides every enrolled user's own timezone, as everywhere else in Moodle.
     */
    public function test_auto_mode_honours_forcetimezone(): void {
        global $USER;
        $this->resetAfterTest();
        $gen = $this->getDataGenerator();

        set_config('timezone', 'Europe/London');
        set_config('forcetimezone', 'Australia/Perth');

        $course = $gen->create_course();
        foreach (['99', 'America/New_York'] as $tz) {
            $user = $gen->create_user(['timezone' => $tz]);
            $gen->enrol_user($user->id, $course->id, 'student');
        }
        $this->setAdminUser();
        $USER->timezone = 'Asia/Tokyo';

        $block = $this->make_block(['mode' => 'auto', 'courseid' => $course->id]);
        $clocks = $this->call($block, 'get_clocks_for_course_users');

        $this->assertSame(['Australia/Perth'], $this->zones($clocks));
    }

    /**
     * A viewer without moodle/course:viewparticipants in the configured course gets no clocks.
     */
    public function test_auto_mode_requires_viewparticipants(): void {
        $this->resetAfterTest();
        $gen = $this->getDataGenerator();

        $course = $gen->create_course();
        $student = $gen->create_user(['timezone' => 'America/New_York']);
        $gen->enrol_user($student->id, $course->id, 'student');

        $block = $this->make_block(['mode' => 'auto', 'courseid' => $course->id]);

        // An outsider cannot see the course's participants.
        $this->setUser($gen->create_user());
        $this->assertSame([], $this->call($block, 'get_clocks_for_course_users'));

        // The same block shows the zones to someone who can.
        $this->setAdminUser();
        $this->assertSame(['America/New_York'], $this->zones($this->call($block, 'get_clocks_for_course_users')));

        // An invalid stored course id degrades to no clocks.
        $broken = $this->make_block(['mode' => 'auto', 'courseid' => 999999]);
        $this->assertSame([], $this->call($broken, 'get_clocks_for_course_users'));
    }

    /**
     * Auto mode renders at most MAX_AUTO_ZONES distinct zones.
     */
    public function test_auto_mode_caps_distinct_zones(): void {
        $this->resetAfterTest();
        $gen = $this->getDataGenerator();

        $course = $gen->create_course();
        $zones = ['Africa/Cairo', 'America/Chicago', 'America/Denver', 'America/New_York', 'America/Sao_Paulo',
            'Asia/Dubai', 'Asia/Kolkata', 'Asia/Tokyo', 'Australia/Perth', 'Europe/Berlin', 'Europe/London',
            'Pacific/Auckland', 'Pacific/Honolulu'];
        foreach ($zones as $tz) {
            $user = $gen->create_user(['timezone' => $tz]);
            $gen->enrol_user($user->id, $course->id, 'student');
        }
        $this->setAdminUser();

        $block = $this->make_block(['mode' => 'auto', 'courseid' => $course->id]);
        $clocks = $this->call($block, 'get_clocks_for_course_users');
        $this->assertCount(block_worldclock::MAX_AUTO_ZONES, $clocks);
    }

    /**
     * Clocks sort by UTC offset in either direction, with an A-Z label tie-break in both.
     */
    public function test_sort_clocks_chronologically(): void {
        $this->resetAfterTest();
        $clocks = [
            ['timezone' => '', 'utcoffset' => '9', 'label' => 'Zulu'],
            ['timezone' => '', 'utcoffset' => '-3.5', 'label' => 'Newfoundland'],
            ['timezone' => '', 'utcoffset' => '9', 'label' => 'alpha'],
            ['timezone' => '', 'utcoffset' => '0', 'label' => 'Greenwich'],
        ];
        $block = $this->make_block([]);

        $asc = $this->call($block, 'sort_clocks_chronologically', [$clocks, 'asc']);
        $this->assertSame(['Newfoundland', 'Greenwich', 'alpha', 'Zulu'], array_column($asc, 'label'));

        $desc = $this->call($block, 'sort_clocks_chronologically', [$clocks, 'desc']);
        $this->assertSame(['alpha', 'Zulu', 'Greenwich', 'Newfoundland'], array_column($desc, 'label'));
    }

    /**
     * Fixed-offset labels.
     */
    public function test_get_offset_label(): void {
        $this->resetAfterTest();
        $block = $this->make_block([]);
        $this->assertSame('UTC', $this->call($block, 'get_offset_label', [0.0]));
        $this->assertSame('UTC+9', $this->call($block, 'get_offset_label', [9.0]));
        $this->assertSame('UTC+5.5', $this->call($block, 'get_offset_label', [5.5]));
        $this->assertSame('UTC-3.5', $this->call($block, 'get_offset_label', [-3.5]));
    }

    /**
     * Manual mode renders the configured zones and escapes stored values at the template sink.
     */
    public function test_get_content_renders_and_escapes(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $block = $this->make_block([
            'mode' => 'manual',
            'timezones' => ['Asia/Tokyo', '<img src=x onerror=alert(1)>'],
            'showutcoffset' => 1,
        ]);
        $text = $block->get_content()->text;

        $this->assertStringContainsString('data-timezone="Asia/Tokyo"', $text);
        $this->assertStringContainsString('(UTC+9)', $text);
        $this->assertStringNotContainsString('<img src=x', $text);
        $this->assertStringContainsString('&lt;img src=x onerror=alert(1)&gt;', $text);
    }

    /**
     * With nothing configured the block shows the "no timezones" notice.
     */
    public function test_get_content_without_timezones_shows_notice(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $block = $this->make_block(['mode' => 'manual']);
        $text = $block->get_content()->text;
        $this->assertStringContainsString(get_string('notimezonesconfigured', 'block_worldclock'), $text);
        $this->assertStringNotContainsString('worldclock-entry', $text);
    }
}
