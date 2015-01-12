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

namespace local_licensing;

use moodle_exception;

defined('MOODLE_INTERNAL') || die;

/**
 * Base factory class.
 */
abstract class base_factory {
    /**
     * Get the class name format string.
     *
     * @var string
     */
    public static function get_class_name_format() {
        static::throw_exception();
    }

    /**
     * Get the fully-qualified class name of a item.
     *
     * @param string $itemname The name of the item.
     *
     * @return string The fully-qualified class name of the item.
     */
    public static function get_class_name($itemname) {
        return sprintf(static::get_class_name_format(), $itemname);
    }

    /**
     * Get an array of all classes.
     *
     * @return string[]
     */
    public static function get_class_names() {
        $types       = static::get_list();
        $typeclasses = array();

        foreach ($types as $type) {
            $types[$type] = static::get_class_name($type);
        }

        return $types;
    }

    /**
     * Get an instance of the specified item.
     *
     * If necessary, you may override this implementation in child classes to
     * enable passing arguments to the constructor.
     *
     * @param string $formname The name of the item.
     *
     * @return \moodleform An instance of the item.
     */
    public static function instance($formname) {
        $classname = static::get_class_name($formname);

        return new $classname();
    }

    /**
     * Throw exception.
     *
     * @return void
     *
     * @throws \moodle_exception Always.
     */
    protected static function throw_exception() {
        throw new moodle_exception('factory:incompleteimplementation',
                                   'local_licensing', '',
                                   get_called_class());
    }
}
