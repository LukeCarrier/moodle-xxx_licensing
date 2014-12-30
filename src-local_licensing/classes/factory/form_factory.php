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

namespace local_licensing\factory;

use local_licensing\base_factory;

defined('MOODLE_INTERNAL') || die;

/**
 * Form manager.
 *
 * A simple factory around our forms to simplify access to them across pages.
 */
class form_factory extends base_factory {
    /**
     * @override \local_licensing\base_factory
     */
    public static function get_class_name_format() {
        return '\\local_licensing\\form\\%s_form';
    }

    /**
     * Get an instance of the specified form.
     *
     * @param string                      $formname The name of the form.
     * @param \moodle_url|string          $action   The URL to submit form data
     *                                              to.
     * @param \local_licensing\base_model $record   The data record from which
     *                                              to source default form input
     *                                              values.
     *
     * @return \moodleform An instance of the form.
     */
    public static function instance($formname, $action=null, $record=null) {
        $classname = static::get_class_name($formname);

        return new $classname($action, array('record' => $record));
    }
}
