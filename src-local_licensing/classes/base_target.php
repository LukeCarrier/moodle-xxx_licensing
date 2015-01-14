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
 * Moodle licensing enrolment plugin.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @author Luke Carrier <luke@tdm.co>
 * @copyright 2014 Luke Carrier, The Development Manager Ltd
 */

namespace local_licensing;

/**
 * Base product type.
 *
 * Product types are extensible, enabling "enrolment" into courses and programs.
 * Other enrolment types may be added via children of this class.
 */
class base_target extends base_pluggable {
    /**
     * Assign a user to the target.
     *
     * @param integer $targetitemid
     * @param integer $assigneeid
     * @param integer $assignerid
     */
    public static function assign_user($targetitemid, $assigneeid, $assignerid) {}

    /**
     * Get the friendly name of the plugin.
     *
     * @return string The friendly name.
     */
    public static function get_name() {
        $type = static::get_type();
        return util::string("product:{$type}");
    }

    /**
     * Get the JOIN and WHERE query fragments for this target.
     *
     * @return string[] An array containing two strings at positions 0 and 1.
     *                  The former index should contain a set of joins to make
     *                  against the user table, aliased as u. The latter index
     *                  should contain a where clause to filter items against
     *                  the :targetitemid.
     */
    public static function get_user_filter_sql() {}

    /**
     * Search for a target for the given user.
     *
     * @param integer $userid The ID of the user we're seeking targets for.
     *
     * @return \local_licensing\model\target The target with the highest
     *                                       priority.
     */
    public static function for_user($userid) {}
}
