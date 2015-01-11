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
 * Distribution model.
 *
 * A distribution is a set of licenses that have been distributed to learners.
 */
class distribution extends base_model {
    /**
     * Record ID.
     *
     * @var integer
     */
    protected $id;

    /**
     * The ID of the allocation.
     *
     * @var integer
     */
    protected $allocationid;

    /**
     * The ID of the product.
     *
     * @var integer
     */
    protected $productid;

    /**
     * Initialiser.
     *
     * @param integer $allocationid
     * @param integer $productid
     */
    public function __construct($allocationid=null, $productid=null) {
        $this->allocationid = $allocationid;
        $this->productid    = $productid;
    }

    /**
     * Get the associated allocation.
     *
     * @return \local_licensing\model\allocation
     */
    public function get_allocation() {
        return allocation::get_by_id($this->allocationid);
    }

    /**
     * Get the count of distributed licences.
     *
     * @return integer The number of distributed licences.
     */
    public function get_count() {
        return count($this->get_user_ids());
    }

    /**
     * Get the associated product.
     *
     * @return \local_licensing\model\product The product.
     */
    public function get_product() {
        return product::get_by_id($this->productid);
    }

    /**
     * Get an array of IDs of users who posess licences in this allocation.
     *
     * @return integer[] An array of user IDs.
     */
    protected function get_user_ids() {
        global $DB;

        $sql = <<<SQL
SELECT u.id
FROM {user} u
LEFT JOIN {lic_licence} l
    ON l.userid = u.id
WHERE l.distributionid = ?
SQL;

        return $DB->get_records_sql($sql, array($this->id));
    }

    /**
     * @override \local_licensing\base_model
     */
    final public static function model_table() {
        return 'lic_distribution';
    }

    /**
     * @override \local_licensing\base_model
     */
    final public static function model_fields() {
        return array(
            'id',
            'allocationid',
            'productid',
        );
    }

    /**
     * @override \local_licensing\base_model
     */
    final public static function model_from_form($data) {
        return new static($data->allocationid, $data->productid);
    }

    /**
     * @override \local_licensing\base_model
     */
    final public function model_to_form() {
        $formdata = parent::model_to_form();

        $formdata->users = implode(',', $this->get_user_ids());

        return $formdata;
    }

    /**
     * @override \local_licensing\base_model
     */
    final public static function model_accessors() {
        return array();
    }
}
