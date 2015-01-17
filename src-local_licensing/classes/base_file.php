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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 * Moodle licensing enrolment plugin.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @author Luke Carrier <luke@tdm.co>
 * @copyright 2014 Luke Carrier, The Development Manager Ltd
 */

namespace local_licensing;

/**
 * Base file options class.
 *
 * Wraps a bunch of options related to specific file areas up in a class for
 * easy transportation.
 */
class base_file {
    /**
     * Get the component name.
     *
     * This should probably never be overridden.
     *
     * @return string The component name.
     */
    public static function get_component() {
        return 'local_licensing';
    }

    /**
     * Get a context.
     *
     * @return \context
     */
    public static function get_context() {}

    /**
     * Get the file area.
     *
     * This should definitely be overridden.
     *
     * @return string The file area name.
     */
    public static function get_file_area() {}

    /**
     * Get the options array.
     *
     * This should probably be overridden.
     *
     * @return string The options array.
     */
    public static function get_options() {}
}
