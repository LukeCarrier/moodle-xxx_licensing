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

namespace local_licensing;

use core_user;
use local_licensing\mailer\allocation_created_mailer;
use local_licensing\mailer\distribution_created_mailer;
use local_licensing\model\allocation;
use local_licensing\model\distribution;

defined('MOODLE_INTERNAL') || die;

/**
 * Event observer.
 *
 * Observing events within our own plugin allows us to decouple unrelated pieces
 * of logic from critical processing and exposes functionality to other plugins.
 */
class observer {
    /**
     * New allocation created.
     *
     * @param \local_licensing\event\allocation_created $event
     *
     * @return void
     */
    public static function allocation_created($event) {
        $allocation = allocation::get_by_id($event->objectid);
        $productset = $allocation->get_product_set();
        $targetset  = $allocation->get_target_set();
        $users      = $targetset->get_distributors();

        $createdby = core_user::get_user($allocation->createdby);

        $a = (object) array(
            'createdbyfullname' => fullname($createdby),
            'id'                => $allocation->id,
            'licencecount'      => $allocation->count,
            'productsetname'    => $productset->name,
            'signoff'           => generate_email_signoff(),
        );

        $mailer = new allocation_created_mailer(core_user::get_noreply_user());

        foreach ($users as $user) {
            $a->userfullname = fullname($user);
            $mailer->mail($user, $a);
        }
    }

    /**
     * New distribution created.
     *
     * @param \local_licensing\event\allocation_created $event
     *
     * @return void
     */
    public static function distribution_created($event) {
        $distribution = distribution::get_by_id($event->objectid);
        $count        = $distribution->get_count();
        $allocation   = $distribution->get_allocation();
        $product      = $distribution->get_product();
        $targetset    = $allocation->get_target_set();
        $users        = $targetset->get_distributors();

        $ispending = $count === distribution::COUNT_BULK_PENDING_CRON;
        $createdby = core_user::get_user($allocation->createdby);

        $a = (object) array(
            'createdbyfullname' => fullname($createdby),
            'id'                => $distribution->id,
            'licencecount'      => $count,
            'productname'       => $product->get_name(),
            'signoff'           => generate_email_signoff(),
        );

        $mailer = new distribution_created_mailer(core_user::get_noreply_user(),
                                                  $ispending);

        foreach ($users as $user) {
            $a->userfullname = fullname($user);
            $mailer->mail($user, $a);
        }
    }
}
