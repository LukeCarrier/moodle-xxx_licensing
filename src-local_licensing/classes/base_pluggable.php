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

use moodle_url;

/**
 * I am so sorry about the name of this class.
 */
abstract class base_pluggable {
    /**
     * Moodle module.
     *
     * @var string
     */
    const MOODLE_MODULE = 'local_licensing';

    /**
     * Get a specific set of products by ID.
     *
     * @param integer[] $ids
     */
    public static function get($ids) {}

    /**
     * Get the base URL of an item.
     *
     * @return \moodle_url The item base URL.
     */
    public static function get_item_base_url() {}

    /**
     * Get a field from the item table.
     *
     * @param integer $itemid The ID of the organisation.
     * @param string  $field  The field we require.
     *
     * @return mixed The field's value.
     */
    protected static function get_item_field($itemid, $field) {
        global $DB;

        return $DB->get_field(static::get_item_table(), $field,
                              array('id' => $itemid));
    }

    /**
     * Get the name of a given item.
     *
     * @param integer $itemid The ID of the item.
     *
     * @return string The name of the item.
     */
    public static function get_item_fullname($itemid) {
        return static::get_item_field($itemid, 'fullname');
    }

    /**
     * Get the item's database table.
     *
     * @return string The name of the database table.
     */
    public static function get_item_table() {}

    /**
     * Get the URL of a given item.
     *
     * @param integer $itemid The ID of the item.
     *
     * @return \moodle_url The URL of the item.
     */
    public static function get_item_url($itemid) {
        $url = new moodle_url(static::get_item_base_url());
        $url->param('id', static::get_item_field($itemid, 'id'));

        return $url;
    }

    /**
     * Get the friendly name of the product plugin.
     *
     * @return string The friendly name.
     */
    public static function get_name() {}

    /**
     * Get the type name.
     *
     * @return string The type name.
     */
    public static function get_type() {
        return get_called_class();
    }

    /**
     * Search for a specific product.
     *
     * @param string $query
     */
    public static function search($query) {}
}
