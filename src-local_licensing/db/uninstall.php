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

/**
 * Uninstall the licensing plugin.
 *
 * Drops database tables created by the plugin.
 */
function xmldb_local_licensing_uninstall() {
    global $DB;
    $dbmgr = $DB->get_manager();

    $tables = array('allocation', 'licence', 'product', 'productset', 'target');

    foreach ($tables as $table) {
        $table = new xmldb_table("lic_{$table}");

        if ($dbmgr->table_exists($table)) {
            $dbmgr->drop_table($table);
        }
    }

    return true;
}
