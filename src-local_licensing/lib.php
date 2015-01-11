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

use local_licensing\capabilities;
use local_licensing\url_generator;
use local_licensing\util;

defined('MOODLE_INTERNAL') || die;

/**
 * Extend the settings navigation block.
 *
 * @param \settings_navigation $navroot
 *
 * @return void
 */
function local_licensing_extends_settings_navigation(settings_navigation $navroot) {
    $navlicensing = $navroot->add(util::string('licensing'), null,
                                  navigation_node::TYPE_SETTING, null,
                                  'local_licensing');

    $context = context_system::instance();
    $taburls = url_generator::tabs();

    foreach ($taburls as $tabname => $taburl) {
        if (capabilities::has_for_tab($tabname, $context)) {
            $tabstring = str_replace('_', '', $tabname);

            $navlicensing->add(util::string($tabstring), $taburl,
                               navigation_node::TYPE_SETTING, null,
                               "local_licensing_{$tabname}");
        }
    }
}
