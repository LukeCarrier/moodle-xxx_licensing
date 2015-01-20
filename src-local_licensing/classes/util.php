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

use context_system;
use html_writer;
use lang_string;

defined('MOODLE_INTERNAL') || die;

class util {
    /**
     * What is the module's name?
     *
     * @var string
     */
    const MOODLE_MODULE = 'local_licensing';

    /**
     * Delta diff a set of objects to determine which need creating and
     * deleting.
     *
     * @param string                        $objecttype
     * @param string                        $objectclass
     * @param string[]                      $types
     * @param \local_licensing\base_model[] $existing
     * @param integer                       $setid
     * @param \stdClass                     $data
     *
     * @return mixed[][] An array containing two arrays; the former containing
     *                   items to create, the latter containing items to delete.
     */
    public static function delta_set($objecttype, $objectclass, $types,
                                     $existing, $setid, $data) {
        $formitems = array();
        foreach ($types as $type) {
            $value = $data->{"{$objecttype}s{$type}"};
            $formitems[$type] = ($value === '') ? array() : explode(',', $value);
        }

        $todelete = array();
        foreach ($existing as $existingitem) {
            if (!in_array($existingitem->itemid, $formitems[$existingitem->type])) {
                $todelete[] = $existingitem;
            }
        }

        $tocreate = array();
        foreach ($formitems as $type => $existingitems) {
            foreach ($existingitems as $itemid) {
                $found = false;
                foreach ($existing as $existingitem) {
                    if (!$found
                            && $existingitem->type == $type
                            && $existingitem->itemid == $itemid) {
                        $found = true;
                    }
                }

                if (!$found) {
                    $tocreate[] = new $objectclass($setid, $type, $itemid);
                }
            }
        }

        return array($tocreate, $todelete);
    }

    /**
     * Get a configuration value.
     *
     * @param string $name The name of the configuration key.
     *
     * @return mixed The configuration value.
     */
    public static function get_config($name) {
        return get_config(static::MOODLE_MODULE, $name);
    }

    /**
     * Get all of the roles with the specified capability.
     *
     * Note that we disregard any other role which explicitly prevents the
     * capability assignment. This will not pose any risk to users following
     * our recommended configuration.
     *
     * @param string $capability The name of the capability.
     *
     * @return integer[] An array of role IDs.
     */
    public static function get_roles_with_capability($capability) {
        global $DB;

        $sql = <<<SQL
SELECT r.id
FROM {role} r
LEFT JOIN {role_capabilities} rc
    ON rc.roleid = r.id
WHERE rc.capability = ?
    AND rc.permission = 1
SQL;
        
        return static::reduce($DB->get_records_sql($sql, array($capability)),
                              'id');
    }

    /**
     * Generate a series of paragraphs from a set of strings.
     *
     * @param string  $stringprefix
     * @param integer $end
     * @param integer $start
     * @param mixed   $a
     * @param string  $module
     */
    public static function paragraphs($stringprefix, $end, $start=1, $a=null,
                                      $module=null) {
        $result = '';
        foreach (range($start, $end) as $paragraph) {
            $paragraphstring = "{$stringprefix}{$paragraph}";

            $result .= html_writer::tag('p', util::string($paragraphstring, $a,
                                                          $module));
        }

        return $result;
    }

    /**
     * Reduce a collection of model objects down to a single field.
     *
     * In the absence of a collection object to wrap sets of models, this is the
     * best we can do for now.
     *
     * @param stdClass[] $collection An array of objects.
     * @param string     $field      An individual field to retain.
     *
     * @return mixed[] An array containing the value of the specified field on
     *                 each object.
     */
    public static function reduce($collection, $field) {
        $result = array();

        foreach ($collection as $item) {
            $result[] = $item->{$field};
        }

        return $result;
    }

    /**
     * Set a configuration value.
     *
     * @param string $name  The name of the configuration key.
     * @param mixed  $value The value of the configuration key.
     *
     * @return void
     */
    public static function set_config($name, $value) {
        return set_config($name, $value, static::MOODLE_MODULE);
    }

    /**
     * Should we display the navigation items?
     *
     * @return boolean Whether or not we should display the navigation items.
     */
    public static function should_show_navigation() {
        return capabilities::has_any(context_system::instance());
    }

    /**
     * Get a language string.
     *
     * @param string          $string The name of the string to retrieve.
     * @param stdClass|string $a      An object or string containing
     *                                substitions to be made to the string's
     *                                value.
     * @param string          $module If retrieving a string from another Moodle
     *                                module, the name of the module.
     *
     * @return \lang_string The language string object.
     */
    public static function string($string, $a=null, $module=null) {
        $module = $module ?: static::MOODLE_MODULE;
        return new lang_string($string, $module, $a);
    }

    /**
     * Get a language string immediately.
     *
     * Returns the actual string instead of the intermediary lang_string
     * object, for inconsistent APIs that don't trigger __toString().
     *
     * @param string          $string The name of the string to retrieve.
     * @param stdClass|string $a      An object or string containing
     *                                substitions to be made to the string's
     *                                value.
     * @param string          $module If retrieving a string from another Moodle
     *                                module, the name of the module.
     *
     * @return string The language string.
     */
    public static function real_string($string, $a=null, $module=null) {
        $module = $module ?: static::MOODLE_MODULE;
        return get_string($string, static::MOODLE_MODULE, $a);
    }

    /**
     * Initialise page requirements.
     *
     * @return void
     */
    public static function init_requirements() {
        global $PAGE;

        $PAGE->requires->css('/local/licensing/style.css');
    }

    /**
     * Does the string start with the substring?
     *
     * @param string $string    The larger of the two strings.
     * @param string $substring The substring to match at the beginning of the
     *                          string.
     *
     * @return boolean|string The remaining text after the substring if matched,
     *                        else false.
     */
    public static function starts_with($string, $substring) {
        if (substr_compare($string, $substring, 0) > 0) {
            return substr($string, strlen($substring));
        }

        return false;
    }
}
