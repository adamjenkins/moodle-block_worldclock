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
 * Live-updating world clock display.
 *
 * @module     block_worldclock/clock
 * @copyright  2026 Adam Jenkins
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define([], function() {

    /**
     * Pad a number to two digits.
     *
     * @param {Number} value
     * @return {String}
     */
    var pad = function(value) {
        return String(value).padStart(2, '0');
    };

    /**
     * Render the time/date for a fixed UTC offset (no DST support).
     *
     * @param {Number} offsetHours
     * @param {Boolean} format24
     * @param {Boolean} showSeconds
     * @param {Boolean} showDate
     * @return {Object} {time: String, date: String}
     */
    var renderFixedOffset = function(offsetHours, format24, showSeconds, showDate) {
        var nowMs = Date.now() + (offsetHours * 3600000);
        var d = new Date(nowMs);

        var hours = d.getUTCHours();
        var minutes = d.getUTCMinutes();
        var seconds = d.getUTCSeconds();
        var suffix = '';

        if (!format24) {
            suffix = hours >= 12 ? ' PM' : ' AM';
            hours = hours % 12;
            if (hours === 0) {
                hours = 12;
            }
        }

        var time = pad(hours) + ':' + pad(minutes);
        if (showSeconds) {
            time += ':' + pad(seconds);
        }
        time += suffix;

        var date = '';
        if (showDate) {
            date = d.getUTCFullYear() + '-' + pad(d.getUTCMonth() + 1) + '-' + pad(d.getUTCDate());
        }

        return {time: time, date: date};
    };

    /**
     * Render the time/date for a named IANA timezone using Intl (handles DST).
     *
     * @param {String} timezone
     * @param {Boolean} format24
     * @param {Boolean} showSeconds
     * @param {Boolean} showDate
     * @return {Object} {time: String, date: String}
     */
    var renderNamedZone = function(timezone, format24, showSeconds, showDate) {
        var locale = document.documentElement.lang || undefined;
        var timeOptions = {
            timeZone: timezone,
            hour: '2-digit',
            minute: '2-digit',
            hour12: !format24,
        };
        if (showSeconds) {
            timeOptions.second = '2-digit';
        }

        var time = new Intl.DateTimeFormat(locale, timeOptions).format(new Date());

        var date = '';
        if (showDate) {
            date = new Intl.DateTimeFormat(locale, {
                timeZone: timezone,
                year: 'numeric',
                month: 'short',
                day: 'numeric',
            }).format(new Date());
        }

        return {time: time, date: date};
    };

    /**
     * Update every clock entry inside the given root element.
     *
     * @param {HTMLElement} root
     * @param {Boolean} format24
     * @param {Boolean} showSeconds
     * @param {Boolean} showDate
     */
    var tick = function(root, format24, showSeconds, showDate) {
        var entries = root.querySelectorAll('.worldclock-entry');
        entries.forEach(function(entry) {
            var timezone = entry.getAttribute('data-timezone');
            var utcoffset = entry.getAttribute('data-utcoffset');
            var result;

            try {
                if (timezone) {
                    result = renderNamedZone(timezone, format24, showSeconds, showDate);
                } else {
                    result = renderFixedOffset(parseFloat(utcoffset) || 0, format24, showSeconds, showDate);
                }
            } catch (e) {
                result = {time: '--:--:--', date: ''};
            }

            var timeNode = entry.querySelector('[data-region="time"]');
            var dateNode = entry.querySelector('[data-region="date"]');
            if (timeNode) {
                timeNode.textContent = result.time;
            }
            if (dateNode) {
                dateNode.textContent = result.date;
            }
        });
    };

    return {
        /**
         * Initialise a world clock block instance.
         *
         * @param {String} elementId id of the block's wrapper element
         * @param {Boolean} format24 whether to use 24-hour time
         * @param {Boolean} showSeconds whether to show seconds
         * @param {Boolean} showDate whether to show the date
         */
        init: function(elementId, format24, showSeconds, showDate) {
            var root = document.getElementById(elementId);
            if (!root) {
                return;
            }

            tick(root, format24, showSeconds, showDate);
            window.setInterval(function() {
                tick(root, format24, showSeconds, showDate);
            }, 1000);
        }
    };
});
