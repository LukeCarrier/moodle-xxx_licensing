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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 * Moodle licensing enrolment plugin.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @author Luke Carrier <luke@tdm.co>
 * @copyright 2014 Luke Carrier, The Development Manager Ltd
 */

namespace local_licensing;

use local_licensing\factory\product_factory;
use local_licensing\model\distribution;

defined('MOODLE_INTERNAL') || die;

/**
 * Enrolment synchronisation cron.
 *
 * Synchronises enrolment data stored within the licensing tables into Moodle's
 * enrolment tables.
 */
class cron {
    /**
     * Last run configuration key.
     *
     * @var string
     */
    const CONFIG_LAST_RUN = 'cronlastrun';

    /**
     * Last run configuration key.
     *
     * @var string
     */
    const CONFIG_RUNNING = 'cronrunning';

    /**
     * Execute the cron.
     */
    public function execute() {
        $running = util::get_config(static::CONFIG_RUNNING);
        if ($running == false) {
            util::set_config(static::CONFIG_RUNNING, true);
        }
        $lastrun = util::get_config(static::CONFIG_LAST_RUN);

        $productclasses = product_factory::get_class_names();

        $distributions = distribution::find_newer_than($lastrun);
        foreach ($distributions as $distribution) {
            $allocation = $distribution->get_allocation();
            $product    = $distribution->get_product();
            $userids    = $distribution->get_user_ids();

            $productclass = $productclasses[$product->type];
            $productclass::enrol($allocation, $distribution, $product, $userids);
        }

        util::set_config(static::CONFIG_LAST_RUN, time());
        util::set_config(static::CONFIG_RUNNING, false);
    }
}
