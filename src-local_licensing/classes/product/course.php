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

use coursecat;
use course_enrolment_manager;
use local_licensing\base_product;
use local_licensing\util;

defined('MOODLE_INTERNAL') || die;

require_once "{$CFG->dirroot}/enrol/locallib.php";

/**
 * Course product type.
 */
class course extends base_product {
    /**
     * @override \local_licensing\base_product
     */
    public static function enrol($allocation, $distribution, $product,
                                 $userids) {
        global $DB, $PAGE;

        $course  = $DB->get_record('course', array('id' => $product->itemid));
        $manager = new course_enrolment_manager($PAGE, $course);

        $instances = $manager->get_enrolment_instances();
        foreach ($instances as $instance) {
            if ($instance->enrol === 'manual') {
                break;
            }
        }
        $plugins = $manager->get_enrolment_plugins();
        if (!array_key_exists('manual', $plugins)) {
            mtrace('manual enrolment not enabled for course ' . $product->itemid);
        }
        $plugin = $plugins['manual'];

        foreach ($userids as $userid) {
            $plugin->enrol_user($instance, $userid, null, $allocation->startdate, $allocation->enddate);
        }
    }

    /**
     * @override \local_licensing\base_product
     */
    public static function get($ids) {
        global $DB;

        list($sql, $params) = $DB->get_in_or_equal($ids);
        $sql = <<<SQL
SELECT id, idnumber, fullname, shortname
FROM {course}
WHERE id {$sql}
SQL;

        return array_values($DB->get_records_sql($sql, $params));
    }

    /**
     * @override \local_licensing\base_pluggable
     */
    public static function get_item_base_url() {
        return '/course/view.php';
    }

    /**
     * @override \local_licensing\base_target
     */
    public static function get_item_table() {
        return 'course';
    }

    /**
     * @override \local_licensing\base_product
     */
    public static function get_name() {
        return util::string('course', null, 'moodle');
    }

    /**
     * @override \local_licensing\base_product
     */
    public static function get_type() {
        return 'course';
    }

    /**
     * @override \local_licensing\base_product
     */
    public static function search($query) {
        $rawcourses = coursecat::search_courses(
                array('search' => $query));

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
