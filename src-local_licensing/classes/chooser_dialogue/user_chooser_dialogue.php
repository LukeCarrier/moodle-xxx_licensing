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

namespace local_licensing\chooser_dialogue;

use local_licensing\base_chooser_dialogue;

defined('MOODLE_INTERNAL') || die;

/**
 * Traget chooser dialogue.
 */
class user_chooser_dialogue extends base_chooser_dialogue {
    /**
     * @override \local_licensing\base_chooser_dialogue
     */
    protected static function get_name_string($type=null) {
        return 'distribution:users';
    }

    /**
     * @override \local_licensing\base_chooser_dialogue
     */
    protected static function get_object_type() {
        return 'user';
    }

    /**
     * @override \local_licensing\base_chooser_dialogue
     */
    protected static function has_subtypes() {
        return false;
    }
}
