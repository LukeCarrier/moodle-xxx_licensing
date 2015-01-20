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
 * Event observers.
 */
$observers = array(

    /*
     * Allocation created.
     */
    array(
        'eventname' => '\local_licensing\event\allocation_created',
        'callback'  => '\local_licensing\observer::allocation_created',
    ),

    /*
     * Distribution created.
     */
    array(
        'eventname' => '\local_licensing\event\distribution_created',
        'callback'  => '\local_licensing\observer::distribution_created',
    ),

    /*
     * Distribution licence creation complete.
     */
    array(
        'eventname' => '\local_licensing\event\distribution_licences_created',
        'callback'  => '\local_licensing\observer::distribution_licences_created',
    ),

    /*
     * User created.
     */
    array(
        'eventname' => '\local_licensing\event\user_created',
        'callback'  => '\local_licensing\observer::user_created',
    ),

);
