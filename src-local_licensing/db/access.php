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

$capabilities = array(

    /*
     * Allocate licenses to organisations.
     */
    'local/licensing:allocatelicenses' => array(
        'captype'      => 'write',
        'contextlevel' => CONTEXT_SYSTEM,

        'riskbitmask' => RISK_CONFIG,

        'archetypes' => array(),

        'clonepermissionsfrom' => 'moodle/site:config',
    ),

    /*
     * Distribute licenses to users within an organisation.
     */
    'local/licensing:distributelicenses' => array(
        'captype'      => 'write',
        'contextlevel' => CONTEXT_SYSTEM,

        'riskbitmask' => RISK_CONFIG,

        'archetypes' => array(),

        'clonepermissionsfrom' => 'moodle/site:config',
    ),

    /*
     * Manage sets of products against which licenses can be allocated.
     */
    'local/licensing:manageproductsets' => array(
        'captype'      => 'write',
        'contextlevel' => CONTEXT_SYSTEM,

        'riskbitmask' => RISK_CONFIG,

        'archetypes' => array(),

        'clonepermissionsfrom' => 'moodle/site:config',
    ),

    /*
     * Manage sets of targets to which licenses can be allocated.
     */
    'local/licensing:managetargetsets' => array(
        'captype'      => 'write',
        'contextlevel' => CONTEXT_SYSTEM,

        'riskbitmask' => RISK_CONFIG,

        'archetypes' => array(),

        'clonepermissionsfrom' => 'moodle/site:config',
    ),

);
