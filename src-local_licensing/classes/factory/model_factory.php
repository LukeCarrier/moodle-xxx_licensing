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
class model_factory extends base_factory {
    /**
     * @override \local_licensing\base_factory
     */
    public static function get_class_name_format() {
        return '\\local_licensing\\model\\%s';
    }

    /**
     * Get an instance of the specified model.
     *
     * @param string             $modelname The name of the model class.
     * @param \moodle_url|string $id        The (optional) ID of the record to
     *                                      retrieve. If not specified, an empty
     *                                      instance of the model will be
     *                                      created.
     *
     * @return \local_licensing\base_model An instance of the model.
     */
    public static function instance($modelname, $id=null) {
        $classname = static::get_class_name($modelname);

        return ($id === null) ? new $classname : $classname::get_by_id($id);
    }

    /**
     * Get an instance of the specified model from user-supplied form data.
     *
     * @param string    $modelname The name of the model class.
     * @param \stdClass $data      The data supplied by the form.
     *
     * @return \local_licensing\base_model An instance of the model.
     */
    public static function instance_from_form($modelname, $data) {
        $classname = static::get_class_name($modelname);

        return $classname::model_from_form($data);
    }
}
