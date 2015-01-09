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
     * Should we display the navigation items?
     *
     * @return boolean Whether or not we should display the navigation items.
     */
    public static function should_show_navigation() {
        return has_any_capability(capabilities::all(),
                                  context_system::instance());
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
