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
 * Upgrade steps for block_worldclock.
 *
 * @package    block_worldclock
 * @copyright  2026 Adam Jenkins <adam@wisecat.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Run upgrade steps for block_worldclock.
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_block_worldclock_upgrade($oldversion) {
    if ($oldversion < 2026062306) {
        // The 'timezoneorder' setting was replaced by 'referencetimezone'.
        unset_config('timezoneorder', 'block_worldclock');
        upgrade_block_savepoint(true, 2026062306, 'worldclock');
    }

    if ($oldversion < 2026062400) {
        // The 'referencetimezone' setting was replaced by a per-instance ascending/descending sort order.
        unset_config('referencetimezone', 'block_worldclock');
        upgrade_block_savepoint(true, 2026062400, 'worldclock');
    }

    return true;
}
