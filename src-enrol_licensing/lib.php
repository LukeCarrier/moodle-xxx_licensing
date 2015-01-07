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

defined('MOODLE_INTERNAL') || die;

/**
 * Entry point for the licensing enrolment plugin.
 */
class enrol_licensing_plugin extends enrol_plugin {
    /**
     * Enrolments we create may not later be customised by users.
     *
     * @override \enrol_plugin
     */
    public function roles_protected() {
        return true;
    }

    /**
     * Users may not manually enrol other users.
     *
     * @override \enrol_plugin
     */
    public function allow_enrol(stdClass $instance) {
        return false;
    }

    /**
     * Users may not manually revoke enrolments.
     *
     * @override \enrol_plugin
     */
    public function allow_unenrol(stdClass $instance) {
        return false;
    }

    /**
     * Users may not manually unerol other users.
     *
     * @override \enrol_plugin
     */
    public function allow_unenrol_user(stdClass $instance, stdClass $ue) {
        return false;
    }

    /**
     * Users may not tweak enrolment period and status.
     *
     * @override \enrol_plugin
     */
    public function allow_manage(stdClass $instance) {
        return false;
    }

    /**
     * @override \enrol_plugin
     */
    // public function get_user_enrolment_actions(course_enrolment_manager $manager, $ue) {}

    /**
     * @override \enrol_plugin
     */
    public function sync_user_enrolments($user) {}
}
