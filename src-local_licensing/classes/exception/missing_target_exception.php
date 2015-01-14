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

namespace local_licensing\exception;

use local_licensing\base_exception;

defined('MOODLE_INTERNAL') || die;

/**
 * Missing target exception.
 *
 * This exception is raised when attempts to identify the containing target of
 * an end user fail.
 */
class missing_target_exception extends base_exception {
    /**
     * @override \local_licensing\base_exception
     */
    public function __construct($code=null, $a=null) {
        parent::__construct('exception:missingtarget', $code, $a);
    }
}
