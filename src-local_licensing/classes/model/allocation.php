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
    protected $targetsetid;

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
     * Initialiser.
     *
     * @param integer $productsetid
     * @param integer $targetsetid
     * @param integer $count
     * @param integer $startdate
     * @param integer $enddate
     */
    public function __construct($productsetid=null, $targetsetid=null,
                                $count=null, $startdate=null, $enddate=null) {
        $this->productsetid = $productsetid;
        $this->targetsetid  = $targetsetid;
        $this->count        = $count;
        $this->startdate    = $startdate;
        $this->enddate      = $enddate;
    }

    /**
     * Get the number of available (remaining) licences.
     *
     * @return integer The number of available licences.
     */
    public function get_available() {
        return $this->get_consumption()->available;
    }

    /**
     * Get the number of consumed licences.
     *
     * @return integer The number of consumed licences.
     */
    public function get_consumed() {
        return $this->get_consumption()->consumed;
    }

    /**
     * Get consumption data.
     *
     * @return \stdClass An object containing the consumed and available
     *                   properties.
     */
    protected function get_consumption() {
        global $DB;

        $sql = <<<SQL
SELECT
    COUNT(l.id)           AS consumed,
    a.count - count(l.id) AS available
FROM mdl_lic_allocation a
LEFT JOIN mdl_lic_distribution d
    ON d.allocationid = a.id
LEFT JOIN mdl_lic_licence l
    ON l.distributionid = d.id
WHERE a.id = ?
SQL;

        return $DB->get_record_sql($sql, array($this->id));
    }

    /**
     * Get the associated product set.
     *
     * @return \local_licensing\model\product_set The product set.
     */
    public function get_product_set() {
        return product_set::get_by_id($this->productsetid);
    }

    /**
     * Get the associated target.
     *
     * @return \local_licensing\model\target The target.
     */
    public function get_target() {
        return target::get_by_id($this->targetsetid);
    }

    /**
     * Get a list of active allocations.
     *
     * @return \stdClass A raw DML record set.
     */
    protected static function get_active_allocations($targetid) {
        global $DB;

        $sql = <<<SQL
SELECT a.id, a.count,
    a.count - count(l.id) AS remaining,
    ps.name AS productsetname
FROM mdl_lic_allocation a
LEFT JOIN mdl_lic_productset ps
    ON ps.id = a.productsetid
LEFT JOIN mdl_lic_distribution d
    ON d.allocationid = a.id
LEFT JOIN mdl_lic_licence l
    ON l.distributionid = d.id
WHERE a.targetid = ?
GROUP BY a.id
HAVING COUNT(l.id) < a.count
SQL;

        return $DB->get_records_sql($sql, array($targetid));
    }

    public static function menu_for_target($targetsetid) {
        $allocations = static::get_active_allocations($targetsetid);
        $menu = array();

        foreach ($allocations as $allocation) {
            $menu[$allocation->id] = util::string('allocation:name',
                                                  $allocation);
        }

        return $menu;
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
            'targetsetid',
            'startdate',
            'enddate',
        );
    }

    /**
     * @override \local_licensing\base_model
     */
    final public static function model_from_form($data) {
        return new static($data->productsetid, $data->targetsetid, $data->count,
                          $data->startdate, $data->enddate);
    }

    /**
     * @override \local_licensing\base_model
     */
    final public static function model_accessors() {
        return array();
    }
}
