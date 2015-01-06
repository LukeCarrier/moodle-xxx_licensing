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

/**
 * Base product type.
 *
 * Product types are extensible, enabling "enrolment" into courses and programs.
 * Other enrolment types may be added via children of this class.
 */
class base_product {
    /**
     * Moodle module.
     *
     * @var string
     */
    const MOODLE_MODULE = 'local_licensing';

    /**
     * Get the type name.
     *
     * @return string The type name.
     */
    public static function get_type() {
        return get_called_class();
    }

    /**
     * Get the name of a given product.
     *
     * @param integer $itemid The ID of the product.
     *
     * @return string The name of the product.
     */
    public static function get_item_name($itemid) {
    }

    /**
     * Get the friendly name of the product plugin.
     *
     * @return string The friendly name.
     */
    public static function get_name() {
        $type = static::get_type();
        return util::string("product:{$type}");
    }

    /**
     * Enrol a given set of users onto this product.
     *
     * @param integer[] $userids An array of IDs of users to enrol in the
     *                           product.
     * @return void
     */
    public static function enrol($userids) {
    }

    /**
     * Get a specific set of products by ID.
     *
     * @param integer[] $ids
     */
    public static function get($ids) {
    }

    /**
     * Search for a specific product.
     *
     * @param string $query
     */
    public static function search($query) {
    }
}
