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
use local_licensing\factory\target_factory;

defined('MOODLE_INTERNAL') || die;

class target_set extends base_model {
    /**
     * Record ID.
     *
     * @var integer
     */
    protected $id;

    /**
     * Name.
     *
     * @var string
     */
    protected $name;

    /**
     * User ID number format string.
     *
     * @var string
     */
    protected $useridnumberformat;

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
     * @param string $name Target set name.
     */
    final public function __construct($name=null, $useridnumberformat=null) {
        $this->name               = $name;
        $this->useridnumberformat = $useridnumberformat;

        $this->set_created();
    }

    /**
     * Format a user's ID number.
     *
     * @param string $idnumber The user's raw ID number.
     *
     * @return string The formatted ID number, ready to be inserted into the
     *                user table's idnumber field.
     */
    final public function format_user_id_number($idnumber) {
        return sprintf($this->useridnumberformat, $idnumber);
    }

    /**
     * Get the targets associated with the target set.
     *
     * @return \local_licensing\model\target[] The products.
     */
    public function get_targets() {
        return target::find_by_targetsetid($this->id);
    }

    /**
     * Retrieve a menu of product sets.
     *
     * @return string[]
     */
    final public static function menu() {
        $targetsets = static::all('name');
        $menu = array();

        foreach ($targetsets as $targetset) {
            $menu[$targetset->id] = $targetset->name;
        }

        return $menu;
    }

    /**
     * @override \local_licensing\base_model
     */
    final public static function model_table() {
        return 'lic_targetset';
    }

    /**
     * @override \local_licensing\base_model
     */
    final public static function model_fields() {
        return array(
            'id',
            'name',
            'useridnumberformat',
            'createdat',
            'createdby',
        );
    }

    /**
     * @override \local_licensing\base_model
     */
    final public static function model_from_form($data) {
        return new static($data->name, $data->useridnumberformat);
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
    final public function model_to_form() {
        $formdata = parent::model_to_form();

        $targetids = array_fill_keys(target_factory::get_list(), array());
        foreach ($this->get_targets() as $target) {
            $targetids[$target->type][] = $target->itemid;
        }
        foreach ($targetids as $type => $ids) {
            $formdata->{"targets{$type}"} = implode(',', $ids);
        }

        return $formdata;
    }
}
