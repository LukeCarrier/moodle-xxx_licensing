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

namespace local_licensing\factory;

use local_licensing\base_factory;

class target_factory extends base_factory {
    /**
     * Product types.
     *
     * @var string[]
     */
    protected static $targettypes = array(
        'organisation',
    );

    /**
     * @override \local_licensing\base_factory
     */
    public static function get_class_name_format() {
        return '\local_licensing\target\%s';
    }

    /**
     * Get a list of all target types.
     *
     * @return string[] The names of all the target types.
     */
    public static function get_list() {
        return static::$targettypes;
    }
}
