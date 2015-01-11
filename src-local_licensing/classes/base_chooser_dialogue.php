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

namespace local_licensing;

use moodle_exception;
use local_licensing\exception\incomplete_implementation_exception;
use local_licensing\exception\input_exception;

defined('MOODLE_INTERNAL') || die;

/**
 * Base chooser dialogue.
 *
 * Ensures all of the required dependencies for the dialogue are set up
 * correctly.
 */
abstract class base_chooser_dialogue {
    /**
     * What is the module's name?
     *
     * @var string
     */
    const MOODLE_MODULE = 'local_licensing';

    /**
     * Array of instance names that exist on a page.
     *
     * @var string[]
     */
    protected static $instances = array();

    /**
     * Add a form field to a form.
     *
     * @param HTML_QuickForm $mform   The form to which the field should be
     *                                added.
     * @param string         $type    The type of the object the field should
     *                                allow the selection of.
     * @param string         $default The default value of the hidden form
     *                                field.
     *
     * @return void
     */
    public static function add_form_field($mform, $default, $type=null) {
        global $PAGE;

        $hassubtypes = static::has_subtypes();

        if ($hassubtypes && $type === null
                || !$hassubtypes && $type !== null) {
            throw new input_exception();
        }

        static::maybe_setup();

        if (!static::can_add_form_field($type)) {
            throw new moodle_exception();
        }

        $ajaxurl    = url_generator::ajax();
        $namestring = static::get_name_string($type);
        $objecttype = static::get_object_type();

        if ($hassubtypes) {
            $name         = "{$objecttype}s{$type}";
            $dialoguename = "licensing-chooserdialogue-{$objecttype}-{$type}";
        } else {
            $name         = $objecttype;
            $dialoguename = "licensing-chooserdialogue-{$objecttype}";
        }

        $params = array(
            'ajaxurl' => $ajaxurl->out_as_local_url(),
            'base'    => $dialoguename,
        );

        if ($hassubtypes) {
            $params['type'] = $type;
        }

        $mform->addElement('text', $name, util::string($namestring));
        $mform->setDefault($name, $default);
        $mform->setType($name, PARAM_TEXT);

        $PAGE->requires->string_for_js($namestring, static::MOODLE_MODULE);
        $PAGE->requires->yui_module("moodle-local_licensing-{$objecttype}chooserdialogue",
                                    "M.local_licensing.init_{$objecttype}_dialogue",
                                    array($params));

        static::$instances[] = $hassubtypes ? $type : true;
    }

    /**
     * Can the field for this object's type be added?
     *
     * @param string $type The object's type.
     *
     * @return boolean Whether or not the form field can be added.
     */
    protected static function can_add_form_field($type) {
        if (static::has_subtypes()) {
            return !in_array($type, static::$instances);
        } else {
            return count(static::$instances) === 0;
        }
    }

    /**
     * Get the name string for the object.
     *
     * @param string $name The name of the object.
     *
     * @return string The object's name string.
     */
    protected static function get_name_string($name) {
        throw new incomplete_implementation_exception();
    }

    /**
     * Get the object type.
     *
     * @return string The object type.
     */
    protected static function get_object_type() {
        throw new incomplete_implementation_exception();
    }

    /**
     * Does the dialogue have subtypes?
     *
     * A dialogue can be complex in that it has both an objecttype (i.e.
     * product) and a type (i.e. course), or simple in that it has only an
     * objecttype (i.e. user).
     *
     * Implementation details differ slightly:
     * -> @todo
     *
     * @return boolean True if the dialogue supports subtypes, else false.
     */
    protected static function has_subtypes() {
        throw new incomplete_implementation_exception();
    }

    /**
     * Set up the dialogue if it hasn't already been set up.
     *
     * @return void
     */
    public static function maybe_setup() {
        if (!count(static::$instances)) {
            static::setup();
        }
    }

    /**
     * Set up the dependencies for the dialogue.
     *
     * @return void 
     */
    protected static function setup() {
        global $PAGE;

        $PAGE->requires->strings_for_js(array(
            'addxs',
        ), static::MOODLE_MODULE);
    }
}
