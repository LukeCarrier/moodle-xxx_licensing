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

namespace local_licensing\target;

use dml_missing_record_exception;
use local_licensing\base_target;
use local_licensing\model\target;
use position_assignment as totara_program_assignment;

defined('MOODLE_INTERNAL') || die;

require_once "{$CFG->dirroot}/totara/core/searchlib.php";
require_once "{$CFG->dirroot}/totara/program/lib.php";

/**
 * Organisation target type.
 */
class organisation extends base_target {
    /**
     * @override \local_licensing\base_target
     */
    public static function assign_user($targetitemid, $assigneeid, $assignerid) {
        $positionassignment = new totara_program_assignment(array(
            'userid' => $assigneeid,
            'type'   => POSITION_TYPE_PRIMARY,
        ));

        $positionassignment->organisationid = $targetitemid;
        assign_user_position($positionassignment);
    }

    /**
     * @override \local_licensing\base_target
     */
    public static function for_user($userid) {
        global $DB;

        /* We can't use position_assignment because it has no fetch_all
         * implementation. */
        $positionassignments = $DB->get_records('pos_assignment',
                                          array('userid' => $userid),
                                          'type ASC', 'id, organisationid');

        foreach ($positionassignments as $positionassignment) {
            try {
                return target::get(array(
                    'type'   => 'organisation',
                    'itemid' => $positionassignment->organisationid,
                ));
            } catch (dml_missing_record_exception $e) {
                /* No target exists for this item -- fail to the next position
                 * assignment or target type */
            }
        }
    }

    /**
     * @override \local_licensing\base_target
     */
    public static function get($ids) {
        global $DB;

        list($sql, $params) = $DB->get_in_or_equal($ids);
        $sql = <<<SQL
SELECT id, idnumber, fullname, shortname
FROM {org}
WHERE id {$sql}
SQL;
        return array_values($DB->get_records_sql($sql, $params));
    }

    /**
     * @override \local_licensing\base_pluggable
     */
    public static function get_item_base_url() {
        return '/totara/hierarchy/item/view.php?prefix=organisation';
    }

    /**
     * @override \local_licensing\base_target
     */
    public static function get_item_table() {
        return 'org';
    }

    /**
     * @override \local_licensing\base_target
     */
    public static function get_name() {
        return util::string('organisation', null, 'totara_hierarchy');
    }

    /**
     * @override \local_licensing\base_target
     */
    public static function get_user_filter_sql() {
        return array(
            'LEFT JOIN {pos_assignment} tpa ON tpa.userid = u.id',
            'tpa.organisationid = :targetitemid',
        );
    }

    /**
     * @override \local_licensing\base_target
     */
    public static function search($query) {
        global $DB;

        $keywords = totara_search_parse_keywords($query);
        $fields   = array('fullname', 'shortname', 'description', 'idnumber');

        list($searchsql, $params)
                = totara_search_get_keyword_where_clause($keywords, $fields);

        $sql = <<<SQL
SELECT hierarchy.id, hierarchy.idnumber, hierarchy.fullname, hierarchy.shortname
FROM {org} hierarchy
WHERE {$searchsql}
ORDER BY sortthread
SQL;

        return array_values($DB->get_records_sql($sql, $params));
    }
}
