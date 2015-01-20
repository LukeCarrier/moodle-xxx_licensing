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

defined('MOODLE_INTERNAL') || die;

require_once "{$CFG->dirroot}/user/lib.php";

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
        global $CFG;

        $user = (object) array(
            'firstname' => $firstname,
            'lastname'  => $lastname,

            'username' => strtolower($username),
            'password' => hash_internal_user_password($password),

            'email'    => $email,
            'idnumber' => $idnumber,

            'auth'        => 'manual',
            'confirmed'   => true,
            'lang'        => $CFG->lang,
            'mnethostid'  => $CFG->mnet_localhost_id,
            'timecreated' => time(),
        );

        /* TODO: what happens if the user supplies a weak password? Should we
         *       set $CFG->passwordpolicy to false before creating users? */
        $user->id = user_create_user($user);

        /* Although an event is raised in user_create_user, it's not easy to
         * uniquely identify the users we're creating in our observer. For this
         * reason, we'll also raise our own. */

        return $user;
    }

    /**
     * Get user details by ID.
     *
     * @param integer[] $ids  The IDs of the users whose details we need to
     *                        retrieve.
     * @param integer $target The (optional) target model object for the target
     *                        users need to be a member of. If not supplied, all
     *                        users with matching IDs will be retrieved.
     *
     * @return \stdClass[] An array of DML records containing the fullname,
     *                     shortname, idnumber and id properties.
     */
    public static function get($ids, $target=null) {
        global $DB;

        list($insql, $idparams) = $DB->get_in_or_equal($ids, SQL_PARAMS_NAMED, 'in');
        $fullnamesql = $DB->sql_fullname();

        if ($target === null) {
            $targetjoinsql  = null;
            $targetwheresql = null;
        } else {
            $targetclass = $target->get_target_class();
            list($targetjoinsql, $targetwheresql, $targetparams)
                    = $targetclass::get_users_in(array($target->itemid));

            $targetwheresql         = "AND {$targetwheresql}";
        }

        $sql = <<<SQL
SELECT u.id, u.idnumber,
    {$fullnamesql} AS fullname,
    {$fullnamesql} AS shortname
FROM {user} u
{$targetjoinsql}
WHERE u.deleted = 0 AND u.confirmed = 1
    AND u.id {$insql}
    {$targetwheresql}
SQL;

        return array_values($DB->get_records_sql($sql,
                                                 $idparams + $targetparams));
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
        list($targetjoinsql, $targetwheresql, $targetparams)
                = $targetclass::get_users_in($target->itemid);

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

        return array_values($DB->get_records_sql($sql,
                                                 $params + $targetparams));
    }

    /**
     * Update the supplied user record.
     *
     * @param \stdClass $user The user record.
     *
     * @return void
     */
    public static function update($user) {
        // TODO: do we want to allow updating the password?
        user_update_user($user, false);
    }
}
