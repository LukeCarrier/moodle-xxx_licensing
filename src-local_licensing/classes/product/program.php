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
use local_licensing\base_product;
use local_licensing\util;

/**
 * Program product type.
 */
class program extends base_product {
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
