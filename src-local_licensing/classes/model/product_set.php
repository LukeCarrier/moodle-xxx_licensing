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
 * A product set is a pre-defined set of products.
 */
class product_set extends base_model {
    /**
     * Record ID.
     *
     * @var integer
     */
    protected $id;

    /**
     * Product set name.
     *
     * @var string
     */
    protected $name;

    /**
     * Created at.
     *
     * @param integer
     */
    protected $createdat;

    /**
     * Created by.
     *
     * @param integer
     */
    protected $createdby;

    /**
     * Initialiser.
     *
     * @param string $name Product set name.
     */
    final public function __construct($name=null) {
        $this->name = $name;

        $this->set_created();
    }

    /**
     * Get the products associated with the product set.
     *
     * @return \local_licensing\model\product[] The products.
     */
    public function get_products() {
        return product::find_by_productsetid($this->id);
    }

    /**
     * Retrieve a menu of product sets.
     *
     * @return string[]
     */
    final public static function menu() {
        $productsets = static::all('name');
        $menu = array();

        foreach ($productsets as $productset) {
            $menu[$productset->id] = $productset->name;
        }

        return $menu;
    }

    /**
     * @override \local_licensing\base_model
     */
    final public static function model_accessors() {
        return array();
    }

    /**
     * @override \local_licensing\base_model
     */
    final public static function model_fields() {
        return array(
            'id',
            'name',
            'createdat',
            'createdby',
        );
    }

    /**
     * @override \local_licensing\base_model
     */
    final public static function model_from_form($data) {
        return new static($data->name);
    }

    /**
     * @override \local_licensing\base_model
     */
    final public static function model_table() {
        return 'lic_productset';
    }

    /**
     * @override \local_licensing\base_model
     */
    final public function model_to_form() {
        $formdata = parent::model_to_form();

        $productids = array_fill_keys(product_factory::get_list(), array());
        foreach ($this->get_products() as $product) {
            $productids[$product->type][] = $product->itemid;
        }
        foreach ($productids as $type => $ids) {
            $formdata->{"products{$type}"} = implode(',', $ids);
        }

        return $formdata;
    }
}
