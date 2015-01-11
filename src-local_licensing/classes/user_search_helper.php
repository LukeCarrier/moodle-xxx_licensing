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

require_once "{$CFG->libdir}/datalib.php";

/**
 * User search helper.
 */
class user_search_helper {
    /**
     * Get user details by ID.
     *
     * @param integer[] $ids The IDs of the users whose details we need to
     *                       retrieve.
     *
     * @return \stdClass[] An array of DML records containing the fullname,
     *                     shortname, idnumber and id properties.
     */
    public static function get($ids) {
        global $DB;

        list($insql, $params) = $DB->get_in_or_equal($ids);
        $fullnamesql = $DB->sql_fullname();

        $sql = <<<SQL
SELECT id, idnumber, {$fullnamesql} AS fullname, {$fullnamesql} AS shortname
FROM {user}
WHERE id {$insql}
SQL;

        return array_values($DB->get_records_sql($sql, $params));
    }

    /**
     * Search for users by the given query.
     *
     * @param string $query The search query.
     *
     * @return \stdClass[] An array of DML records containing the fullname,
     *                     shortname, idnumber and id properties.
     */
    public static function search($query) {
        $rawusers = search_users(SITEID, null, $query);

        $users = array();
        foreach ($rawusers as $user) {
            /* We can't use fullname() here, as we don't have all of the
             * required fields. There isn't a way for us to add them, either. */
            $name = "{$user->firstname} {$user->lastname}";

            $users[] = (object) array(
                'id'        => $user->id,
                'idnumber'  => '',        // This isn't in the fields either
                'fullname'  => $name,
                'shortname' => $name,
            );
        }

        return $users;
    }
}
