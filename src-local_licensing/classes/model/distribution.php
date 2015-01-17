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
use local_licensing\util;

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
     * @param integer $allocationid
     * @param integer $productid
     */
    public function __construct($allocationid=null, $productid=null) {
        $this->allocationid = $allocationid;
        $this->productid    = $productid;

        $this->set_created();
    }

    /**
     * Get active distributions.
     *
     * @param integer $targetsetid An (optional) ID of a specific target set,
     *                             used to filter the distributions.
     *
     * @return \local_licensing\model\distribution[]
     */
    public static function get_active_distributions($targetsetid=null) {
        global $DB;

        $timestamp = static::model_get_unix_timestamp();

        $sql = <<<SQL
SELECT d.*
FROM {lic_distribution} d
LEFT JOIN {lic_allocation} a
    ON a.id = d.allocationid
WHERE a.startdate <= {$timestamp}
    AND a.enddate >= {$timestamp}
SQL;
        $params = array();

        if ($targetsetid !== null) {
            $sql      .= ' AND a.targetsetid = 1';
            $params[]  = $targetsetid;
        }

        $records = $DB->get_records_sql($sql, $params);

        return static::model_from_many_dml($records);
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
     * Get the licences associated with this distribution.
     *
     * @return \local_licensing\model\licence[] The licences.
     */
    public function get_licences() {
        return licence::find_by_distributionid($this->id);
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
    public function get_user_ids() {
        global $DB;

        $licences = licence::find_by_distributionid($this->id);

        return util::reduce($licences, 'id');
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
            'createdat',
            'createdby',
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
