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

namespace local_licensing\event;

use core_user;
use local_licensing\base_event;
use local_licensing\url_generator;
use local_licensing\util;

defined('MOODLE_INTERNAL') || die;

/**
 * User created.
 */
class user_created extends base_event {
    /**
     * @override \local_licensing\base_event
     */
    public function get_description() {
        return util::real_string('event:usercreated:desc',
                                 $this->get_description_subs());
    }

    /**
     * @override \local_licensing\base_event
     */
    public function get_description_subs() {
        $a = parent::get_description_subs();

        $a->relateduserid = $this->relateduserid;

        return $a;
    }

    /**
     * @override \local_licensing\base_event
     */
    public function get_legacy_logdata() {
        $logdata = parent::get_legacy_logdata();
        $logdata[static::LEGACY_LOGDATA_ACTION] = 'licence user create';

        return $logdata;
    }

    /**
     * @override \local_licensing\base_event
     */
    public static function get_name() {
        return util::string('event:usercreated');
    }

    /**
     * @override \local_licensing\base_event
     */
    public function get_url() {
        return url_generator::edit_user($this->objectid);
    }

    /**
     * @override \local_licensing\base_event
     */
    public function init() {
        global $CFG;

        // Work around for MDL-43661
        $edulevel = ($CFG->version >= 2014111000) ? 'edulevel' : 'level';

        $this->data['crud']        = 'c';
        $this->data[$edulevel]     = static::LEVEL_OTHER;
        $this->data['objecttable'] = 'user';
    }

    /**
     * Rapidly instantiate the event.
     *
     * @param \stdClass       $user     The affected user.
     * @param string          $password The cleartext user password.
     * @param \context_system $context  The system context.
     *
     * @return \local_licensing\event\user_created The event.
     */
    final public static function instance($user, $password, $context) {
        return static::create(array(
            'objectid' => $user->id,
            'context'  => $context,
            'other'    => array(
                // We need these for mailing the "welcome" notification
                'password' => $password,
                'username' => $user->username,
            ),
        ));
    }
}
