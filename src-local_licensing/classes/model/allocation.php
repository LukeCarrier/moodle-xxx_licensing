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
 * Allocation model.
 *
 * An allocation is a representation of a set of allocated licences.
 */
class allocation extends base_model {
    /**
     * Record ID.
     *
     * @var integer
     */
    protected $id;

    /**
     * The total number of licenses which can be distributed.
     *
     * @var integer
     */
    protected $count;

    /**
     * The ID of the set of offered products.
     *
     * @var integer
     */
    protected $productsetid;

    /**
     * The ID of the target that owns the allocation.
     *
     * @var integer
     */
    protected $targetid;

    /**
     * Start date.
     *
     * @var integer
     */
    protected $startdate;

    /**
     * End date.
     *
     * @var integer
     */
    protected $enddate;

    /**
     * Get the number of consumed licences.
     *
     * @return integer The number of consumed licences.
     */
    public function get_consumed() {
        return licence::count(array('allocationid' => $this->id));
    }

    /**
     * Get the associated product set.
     *
     * @return \local_licensing\model\product_set The product set.
     */
    public function get_product_set() {
        product_set::get_by_id($this->productsetid);
    }

    /**
     * Get the number of remaining licences.
     *
     * @return integer The number of remaining licences.
     */
    public function get_remaining() {
        return $this->count - $this->get_consumed();
    }

    /**
     * @override \local_licensing\base_model
     */
    final public static function model_table() {
        return 'lic_allocation';
    }

    /**
     * @override \local_licensing\base_model
     */
    final public static function model_fields() {
        return array(
            'id',
            'count',
            'productsetid',
            'targetid',
            'startdate',
            'enddate',
        );
    }

    /**
     * @override \local_licensing\base_model
     */
    final public static function model_accessors() {
        return array();
    }
}
