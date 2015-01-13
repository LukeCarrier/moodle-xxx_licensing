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

/**
 * A target represents an individual group of users within a wider set of
 * groups.
 */
class target extends base_model {
    /**
     * Record ID.
     *
     * @var integer
     */
    protected $id;

    /**
     * The ID of the target item.
     *
     * This value will be passed to the target class when location the target.
     *
     * @var integer
     */
    protected $itemid;

    /**
     * Product type.
     *
     * The value of this field should mirror the name of a class in the
     * \local_licensing\target namespace.
     *
     * @var string
     */
    protected $type;

    /**
     * Product set ID.
     *
     * @var integer
     */
    protected $targetsetid;

    /**
     * Initialiser.
     *
     * @param integer $targetsetid
     * @param string  $type
     * @param integer $itemid
     */
    public function __construct($targetsetid=null, $type=null, $itemid=null) {
        $this->targetsetid = $targetsetid;
        $this->type         = $type;
        $this->itemid       = $itemid;
    }

    /** 
     * Get the name of the target.
     *
     * @return string The name of the target.
     */
    final public function get_name() {
        $targetclass = $this->get_target_class();

        return $targetclass::get_item_fullname($this->itemid);
    }

    /**
     * Get the target class.
     *
     * @return string The target class.
     */
    final public function get_target_class() {
        return target_factory::get_class_name($this->type);
    }

    /**
     * Get the URL of the target.
     *
     * @return \moodle_url The URL.
     */
    final public function get_url() {
        $targetclass = $this->get_target_class();

        return $targetclass::get_item_url($this->itemid);
    }

    /**
     * @override \local_licensing\base_model
     */
    final public static function model_table() {
        return 'lic_target';
    }

    /**
     * @override \local_licensing\base_model
     */
    final public static function model_fields() {
        return array(
            'id',
            'itemid',
            'targetsetid',
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
