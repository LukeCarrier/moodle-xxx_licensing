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

use context_program;
use coursecat;
use individuals_category as totara_program_individuals_category;
use local_licensing\base_product;
use local_licensing\util;
use program as totara_program;
use totara_program\event\program_assignmentsupdated
        as totara_program_assignmentsupdated;

require_once "{$CFG->dirroot}/totara/program/lib.php";

/**
 * Program product type.
 */
class program extends base_product {
    /**
     * @override \local_licensing\base_product
     */
    public static function enrol($allocation, $distribution, $product,
                                 $userids) {
        global $DB;

        /* This logic is mostly descended from that in upstream
         * /totara/program/edit_assignments.php, including emulating the same
         * format of the data parameter passed to the assignment categories.
         *
         * The notable exception is that we exclusively update the
         * individuals_category. */
        $program  = new totara_program($product->itemid);
        $category = new totara_program_individuals_category();

        $completiondate = date('d/m/Y', $allocation->enddate);

        $data = (object) array(
            'id' => $program->id,

            'item'       => array(
                ASSIGNTYPE_INDIVIDUAL => array_fill_keys($userids, '1'),
            ),
            'completion' => array(
                ASSIGNTYPE_INDIVIDUAL => array_fill_keys($userids,
                                                         $completiondate)
            ),
        );

        $category->update_assignments($data);

        $assignments = $program->get_assignments();
        $assignments->init_assignments($program->id);
        $program->update_learner_assignments();

        $programrecord = (object) array(
            'id'           => $program->id,
            'timemodified' => time(),
            'usermodified' => $distribution->createdby,
        );
        $DB->update_record('prog', $programrecord);

        $assignmentrecords = array();
        foreach ($assignments as $assignment) {
            $assignmentrecords[] = (array) $assignment;
        }

        $event = totara_program_assignmentsupdated::create(array(
            'objectid' => $program->id,
            'context'  => context_program::instance($program->id),
            'userid'   => $distribution->createdby,
            'other'    => array(
                'assignments' => $assignmentrecords,
            ),
        ));
        $event->trigger();
    }

    /**
     * @override \local_licensing\base_product
     */
    public static function get($ids) {
        global $DB;

        list($sql, $params) = $DB->get_in_or_equal($ids);
        $sql = <<<SQL
SELECT id, idnumber, fullname, shortname
FROM {prog}
WHERE id {$sql}
SQL;

        return array_values($DB->get_records_sql($sql, $params));
    }

    /**
     * @override \local_licensing\base_pluggable
     */
    public static function get_item_base_url() {
        return '/totara/program/view.php?viewtype=program';
    }

    /**
     * @override \local_licensing\base_target
     */
    public static function get_item_table() {
        return 'prog';
    }

    /**
     * @override \local_licensing\base_product
     */
    public static function get_name() {
        return util::string('program', null, 'totara_program');
    }

    /**
     * @override \local_licensing\base_product
     */
    public static function get_type() {
        return 'program';
    }

    /**
     * @override \local_licensing\base_product
     */
    public static function search($query) {
        $rawprograms = coursecat::search_programs(
                array('search' => $query));

        $programs = array();
        foreach ($rawprograms as $program) {
            $programs[] = (object) array(
                'id'        => $program->id,
                'idnumber'  => $program->idnumber,
                'fullname'  => $program->fullname,
                'shortname' => $program->shortname,
            );
        }

        return $programs;
    }
}
