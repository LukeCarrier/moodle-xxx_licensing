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

namespace local_licensing\product;

use core_course\management\helper;
use local_licensing\base_product;
use local_licensing\util;

/**
 * Course product type.
 */
class course extends base_product {
    /**
     * @override \local_licensing\base_product
     */
    public static function get_name() {
        return util::string('course', null, 'moodle');
    }

    /**
     * @override \local_licensing\base_product
     */
    public static function search($query) {
        list($rawcourses, $count, $totalcount)
                = helper::search_courses($query, null, null);

        $courses = array();
        foreach ($rawcourses as $course) {
            $courses[] = (object) array(
                'id'        => $course->id,
                'idnumber'  => $course->idnumber,
                'fullname'  => $course->fullname,
                'shortname' => $course->shortname,
            );
        }

        return $courses;
    }
}
