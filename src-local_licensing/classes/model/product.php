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
use local_licensing\factory\product_factory;

defined('MOODLE_INTERNAL') || die;

/**
 * A product represents an individual item which can be distributed to users.
 */
class product extends base_model {
    /**
     * Record ID.
     *
     * @var integer
     */
    protected $id;

    /**
     * The ID of the product item.
     *
     * This value will be passed to the product class when location the product.
     *
     * @var integer
     */
    protected $itemid;

    /**
     * Product type.
     *
     * The value of this field should mirror the name of a class in the
     * \local_licensing\product namespace.
     *
     * @var string
     */
    protected $type;

    /**
     * Product set ID.
     *
     * @var integer
     */
    protected $productsetid;

    /**
     * Initialiser.
     *
     * @param integer $productsetid
     * @param string  $type
     * @param integer $itemid
     */
    public function __construct($productsetid=null, $type=null, $itemid=null) {
        $this->productsetid = $productsetid;
        $this->type         = $type;
        $this->itemid       = $itemid;
    }

    /** 
     * Get the name of the product.
     *
     * @return string The name of the product.
     */
    final public function get_name() {
        $productclass = $this->get_product_class();

        return $productclass::get_item_fullname($this->itemid);
    }

    /** 
     * Get the URL of the product.
     *
     * @return \moodle_url The URL of the product.
     */
    final public function get_url() {
        $productclass = $this->get_product_class();

        return $productclass::get_item_url($this->itemid);
    }

    /**
     * Get the product class.
     *
     * @return \local_licensing\base_product The product class.
     */
    public function get_product_class() {
        return product_factory::get_class_name($this->type);
    }

    /**
     * @override \local_licensing\base_model
     */
    final public static function model_table() {
        return 'lic_product';
    }

    /**
     * @override \local_licensing\base_model
     */
    final public static function model_fields() {
        return array(
            'id',
            'itemid',
            'productsetid',
            'type',
        );
    }

    /**
     * @override \local_licensing\base_model
     */
    final public static function model_accessors() {
        return array();
    }
}
