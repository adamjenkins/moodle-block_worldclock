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
 * @copyright  2026 Adam Jenkins <adam@wisecat.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define([], function() {

    /** CSS classes applied to an entry for the waking-hours background colours. */
    var BG_CLASSES = ['worldclock-bg-night', 'worldclock-bg-twilight', 'worldclock-bg-day'];

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
     * Get the local Date object for a fixed UTC offset (no DST support).
     *
     * @param {Number} offsetHours
     * @return {Date}
     */
    var dateForFixedOffset = function(offsetHours) {
        return new Date(Date.now() + (offsetHours * 3600000));
    };

    /**
     * Get the ISO (yyyy-mm-dd) calendar date for either a named timezone or a fixed UTC offset.
     *
     * @param {String} timezone
     * @param {String} utcoffset
     * @return {String}
     */
    var isoDateFor = function(timezone, utcoffset) {
        try {
            if (timezone) {
                return new Intl.DateTimeFormat('en-CA', {
                    timeZone: timezone,
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                }).format(new Date());
            }
            var d = dateForFixedOffset(parseFloat(utcoffset) || 0);
            return d.getUTCFullYear() + '-' + pad(d.getUTCMonth() + 1) + '-' + pad(d.getUTCDate());
        } catch (e) {
            return '';
        }
    };

    /**
     * Get the current 24-hour hour (0-23) for either a named timezone or a fixed UTC offset.
     *
     * @param {String} timezone
     * @param {String} utcoffset
     * @return {Number}
     */
    var hour24For = function(timezone, utcoffset) {
        if (timezone) {
            var parts = new Intl.DateTimeFormat('en-GB', {
                timeZone: timezone,
                hour: '2-digit',
                hourCycle: 'h23',
            }).formatToParts(new Date());
            var hourPart = parts.find(function(part) {
                return part.type === 'hour';
            });
            return hourPart ? parseInt(hourPart.value, 10) : 0;
        }
        return dateForFixedOffset(parseFloat(utcoffset) || 0).getUTCHours();
    };

    /**
     * Determine whether it is currently daytime in the given zone.
     *
     * @param {String} timezone
     * @param {String} utcoffset
     * @param {Number} dayStart hour (24h) at which daytime starts
     * @param {Number} nightStart hour (24h) at which nighttime starts
     * @return {Boolean}
     */
    var isDaytime = function(timezone, utcoffset, dayStart, nightStart) {
        try {
            var hour = hour24For(timezone, utcoffset);
            return hour >= dayStart && hour < nightStart;
        } catch (e) {
            return true;
        }
    };

    /**
     * Work out which waking-hours background period the given hour falls into.
     *
     * Boundaries are treated circularly, so the period that applies is the one whose
     * start hour is the closest one at or before the given hour, wrapping past midnight.
     *
     * @param {Number} hour
     * @param {Number} nightStart
     * @param {Number} morningStart
     * @param {Number} dayStart
     * @param {Number} eveningStart
     * @return {String} one of the BG_CLASSES values
     */
    var backgroundClassForHour = function(hour, nightStart, morningStart, dayStart, eveningStart) {
        var boundaries = [
            {hour: nightStart, cls: 'worldclock-bg-night'},
            {hour: morningStart, cls: 'worldclock-bg-twilight'},
            {hour: dayStart, cls: 'worldclock-bg-day'},
            {hour: eveningStart, cls: 'worldclock-bg-twilight'},
        ].sort(function(a, b) {
            return a.hour - b.hour;
        });

        var result = boundaries[boundaries.length - 1].cls;
        boundaries.forEach(function(boundary) {
            if (boundary.hour <= hour) {
                result = boundary.cls;
            }
        });
        return result;
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
        var d = dateForFixedOffset(offsetHours);

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
     * Render the time and date text for a single clock entry.
     *
     * @param {HTMLElement} entry
     * @param {String} timezone
     * @param {String} utcoffset
     * @param {Boolean} effectiveShowDate
     * @param {Object} options see init() for the supported keys
     */
    var updateTimeAndDate = function(entry, timezone, utcoffset, effectiveShowDate, options) {
        var result;
        try {
            if (timezone) {
                result = renderNamedZone(timezone, options.format24, options.showSeconds, effectiveShowDate);
            } else {
                result = renderFixedOffset(
                    parseFloat(utcoffset) || 0,
                    options.format24,
                    options.showSeconds,
                    effectiveShowDate
                );
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
    };

    /**
     * Update the day/night icon (and its screen reader text) for a single clock entry.
     *
     * @param {HTMLElement} entry
     * @param {String} timezone
     * @param {String} utcoffset
     * @param {Object} options see init() for the supported keys
     */
    var updateIcon = function(entry, timezone, utcoffset, options) {
        var iconNode = entry.querySelector('[data-region="icon"]');
        var iconLabelNode = entry.querySelector('[data-region="iconlabel"]');
        if (!iconNode && !iconLabelNode) {
            return;
        }

        var icon = '';
        var label = '';
        if (options.showIcon) {
            var daytime = isDaytime(timezone, utcoffset, options.iconDayStart, options.iconNightStart);
            icon = daytime ? '☀️' : '🌙';
            label = daytime ? options.daytimeLabel : options.nighttimeLabel;
        }

        if (iconNode) {
            iconNode.textContent = icon;
        }
        if (iconLabelNode) {
            iconLabelNode.textContent = label;
        }
    };

    /**
     * Update the waking-hours background colour for a single clock entry.
     *
     * @param {HTMLElement} entry
     * @param {String} timezone
     * @param {String} utcoffset
     * @param {Object} options see init() for the supported keys
     */
    var updateBackground = function(entry, timezone, utcoffset, options) {
        entry.classList.remove.apply(entry.classList, BG_CLASSES);
        if (!options.wakingHoursBg) {
            return;
        }

        try {
            var hour = hour24For(timezone, utcoffset);
            entry.classList.add(backgroundClassForHour(
                hour,
                options.nightStart,
                options.morningStart,
                options.dayStart,
                options.eveningStart
            ));
        } catch (e) {
            // Leave background unset if the hour could not be determined.
        }
    };

    /**
     * Update every clock entry inside the given root element.
     *
     * @param {HTMLElement} root
     * @param {Object} options see init() for the supported keys
     */
    var tick = function(root, options) {
        var userTimezone = root.getAttribute('data-user-timezone');
        var userUtcoffset = root.getAttribute('data-user-utcoffset');
        var userIsoDate = isoDateFor(userTimezone, userUtcoffset);

        var entries = root.querySelectorAll('.worldclock-entry');
        entries.forEach(function(entry) {
            var timezone = entry.getAttribute('data-timezone');
            var utcoffset = entry.getAttribute('data-utcoffset');
            var entryIsoDate = isoDateFor(timezone, utcoffset);
            var dateDiffers = userIsoDate !== '' && entryIsoDate !== '' && entryIsoDate !== userIsoDate;
            var effectiveShowDate = options.showDate && (!options.showDateOnlyDiff || dateDiffers);

            updateTimeAndDate(entry, timezone, utcoffset, effectiveShowDate, options);
            updateIcon(entry, timezone, utcoffset, options);
            updateBackground(entry, timezone, utcoffset, options);
        });
    };

    return {
        /**
         * Initialise a world clock block instance.
         *
         * @param {String} elementId id of the block's wrapper element
         * @param {Object} options
         * @param {Boolean} options.format24 whether to use 24-hour time
         * @param {Boolean} options.showSeconds whether to show seconds
         * @param {Boolean} options.showDate whether to show the date at all
         * @param {Boolean} options.showDateOnlyDiff only show the date when it differs from the user's date
         * @param {Boolean} options.showIcon whether to show the day/night icon
         * @param {String} options.daytimeLabel localised "Daytime" string, for the icon's screen reader text
         * @param {String} options.nighttimeLabel localised "Nighttime" string, for the icon's screen reader text
         * @param {Number} options.iconDayStart hour the day/night icon switches to the sun icon
         * @param {Number} options.iconNightStart hour the day/night icon switches to the moon icon
         * @param {Boolean} options.wakingHoursBg whether to colour the background by time of day
         * @param {Number} options.nightStart hour the overnight background period starts
         * @param {Number} options.morningStart hour the morning background period starts
         * @param {Number} options.dayStart hour the daytime background period starts
         * @param {Number} options.eveningStart hour the evening background period starts
         */
        init: function(elementId, options) {
            var root = document.getElementById(elementId);
            if (!root) {
                return;
            }

            tick(root, options);
            window.setInterval(function() {
                tick(root, options);
            }, 1000);
        }
    };
});
