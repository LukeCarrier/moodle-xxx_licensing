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

use context_user;
use core\event\user_created;
use dml_write_exception;

/**
 * User search helper.
 */
class user_helper {
    /**
     * Create a user.
     *
     * @param string  $firstname The new user's first name.
     * @param string  $lastname  The new user's last name.
     * @param string  $username  The new user's username.
     * @param string  $password  The new user's password.
     * @param string  $email     The new user's email address.
     * @param string  $idnumber  The new user's ID number.
     * @param integer $createdby ID of the user that triggered the user
     *                           creation.
     *
     * @return void
     */
    public static function create($firstname, $lastname, $username, $password,
                                  $email, $idnumber) {
        global $CFG, $DB;

        $creationtime = time();

        $user = (object) array(
            'firstname' => $firstname,
            'lastname'  => $lastname,

            'username' => strtolower($username),
            'password' => $password,

            'email'    => $email,
            'idnumber' => $idnumber,

            'auth'        => 'manual',
            'confirmed'   => true,
            'lang'        => $CFG->lang,
            'mnethostid'  => $CFG->mnet_localhost_id,
            'timecreated' => $creationtime,
        );

        $user->id = $DB->insert_record('user', $user);

        $event = user_created::create(array(
            'objectid' => $user->id,
            'context' => context_user::instance($user->id),
        ));
        $event->trigger();

        return $user;
    }

    /**
     * Get user details by ID.
     *
     * @param integer[] $ids  The IDs of the users whose details we need to
     *                        retrieve.
     * @param integer $target The target model object for the target users
     *                        need to be a member of.
     *
     * @return \stdClass[] An array of DML records containing the fullname,
     *                     shortname, idnumber and id properties.
     */
    public static function get($ids, $target) {
        global $DB;

        list($insql, $params) = $DB->get_in_or_equal($ids, SQL_PARAMS_NAMED, 'in');
        $fullnamesql = $DB->sql_fullname();

        $targetclass = $target->get_target_class();
        list($targetjoinsql, $targetwheresql)
                = $targetclass::get_user_filter_sql();
        $params['targetitemid'] = $target->itemid;

        $sql = <<<SQL
SELECT u.id, u.idnumber,
    {$fullnamesql} AS fullname,
    {$fullnamesql} AS shortname
FROM {user} u
{$targetjoinsql}
WHERE u.deleted = 0 AND u.confirmed = 1
    AND u.id {$insql}
    AND {$targetwheresql}
SQL;

        return array_values($DB->get_records_sql($sql, $params));
    }

    /**
     * Search for users by the given query.
     *
     * @param string  $query  The search query.
     * @param integer $target The target model object for the target users
     *                        need to be a member of.
     *
     * @return \stdClass[] An array of DML records containing the fullname,
     *                     shortname, idnumber and id properties.
     */
    public static function search($query, $target) {
        global $DB;

        /* This function has been closely modeled after search_users in
         * /lib/datalib.php. We have to provide our own implementation in order
         * to allow filtering users by their assigned targets. */

        $fullnamesql = $DB->sql_fullname('u.firstname', 'u.lastname');

        $params = array();
        $fields = array(
            $fullnamesql,
            'u.email',
        );

        $fieldswhere = array();
        foreach ($fields as $index => $field) {
            $param = "search{$index}";

            $fieldswhere[]  = $DB->sql_like($field, ":{$param}", false);
            $params[$param] = "%{$query}%";
        }
        $fieldswheresql = '(' . implode(' OR ', $fieldswhere) . ')';

        $targetclass = $target->get_target_class();
        list($targetjoinsql, $targetwheresql)
                = $targetclass::get_user_filter_sql();
        $params['targetitemid'] = $target->itemid;

        $sql = <<<SQL
SELECT u.id, u.idnumber,
    {$fullnamesql} AS fullname,
    {$fullnamesql} AS shortname
FROM {user} u
    {$targetjoinsql}
WHERE u.deleted = 0 AND u.confirmed = 1
    AND {$fieldswheresql}
    AND {$targetwheresql}
SQL;

        return array_values($DB->get_records_sql($sql, $params));
    }
}
