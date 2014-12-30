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

namespace local_licensing\model;

use local_licensing\base_model;

defined('MOODLE_INTERNAL') || die;

/**
 * Licence model.
 *
 * Licences are singular allocations which have been converted to enrolments.
 */
class licence extends base_model {
    /**
     * Record ID.
     *
     * @var integer
     */
    protected $id;

    /**
     * Allocation ID.
     *
     * @var integer
     */
    protected $allocationid;

    /**
     * Product ID.
     *
     * @var integer
     */
    protected $productid;

    /**
     * User ID.
     *
     * @var integer
     */
    protected $userid;

    /**
     * Get the associated product.
     *
     * @return \local_licensing\model\product The product.
     */
    public function get_product() {
        return product::get_by_id($this->productid);
    }

    /**
     * @override \local_licensing\base_model
     */
    final public static function model_table() {
        return 'lic_licence';
    }

    /**
     * @override \local_licensing\base_model
     */
    final public static function model_fields() {
        return array(
            'id',
            'allocationid',
            'productid',
            'userid',
        );
    }

    /**
     * @override \local_licensing\base_model
     */
    final public static function model_accessors() {
        return array();
    }
}
