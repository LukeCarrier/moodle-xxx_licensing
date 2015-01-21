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
use html_writer;
use local_licensing\event\user_created;
use local_licensing\mailer\allocation_created_mailer;
use local_licensing\mailer\distribution_created_mailer;
use local_licensing\mailer\distribution_licences_created_mailer;
use local_licensing\mailer\enrolment_created_mailer;
use local_licensing\mailer\user_created_mailer;
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
     * @param \local_licensing\event\distribution_created $event
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

        if ($count === distribution::COUNT_BULK_PENDING_CRON) {
            $count = util::real_string('distribution:count:bulkpending');
        }

        $createdby = core_user::get_user($allocation->createdby);

        $a = (object) array(
            'createdbyfullname' => fullname($createdby),
            'id'                => $distribution->id,
            'licencecount'      => $count,
            'productname'       => $product->get_name(),
            'signoff'           => generate_email_signoff(),
        );

        $mailer = new distribution_created_mailer(core_user::get_noreply_user());
        foreach ($users as $user) {
            $a->userfullname = fullname($user);
            $mailer->mail($user, $a);
        }
    }

    /**
     * All licences for a distribution created.
     *
     * @param \local_licensing\event\distribution_licences_created $event
     *
     * @return void
     */
    public static function distribution_licences_created($event) {
        $distribution = distribution::get_by_id($event->objectid);
        $allocation   = $distribution->get_allocation();
        $targetset    = $allocation->get_target_set();
        $users        = $targetset->get_distributors();
        $product      = $distribution->get_product();

        $learners    = $distribution->get_users();
        $noreplyuser = core_user::get_noreply_user();
        $site        = get_site();
        $signoff     = generate_email_signoff();

        // This belongs in a renderer
        $learnerlistitems = '';
        foreach ($learners as $learner) {
            $learnerlistitems
                    .= html_writer::tag('li', fullname($learner) . " ($learner->id)");
        }

        $a = (object) array(
            'id'          => $distribution->id,
            'learnerlist' => html_writer::tag('ul', $learnerlistitems),
            'signoff'     => $signoff,
        );

        $mailer = new distribution_licences_created_mailer($noreplyuser);
        foreach ($users as $user) {
            $a->userfullname = fullname($user);
            $mailer->mail($user, $a);
        }

        $a = (object) array(
            'allocationenddate'   => util::date($allocation->enddate),
            'allocationstartdate' => util::date($allocation->startdate),
            'loginurl'            => (string) url_generator::login(),
            'productname'         => $product->get_name(),
            'sitefullname'        => $site->fullname,
            'siteshortname'       => $site->shortname,
            'signoff'             => $signoff,
        );

        $mailer = new enrolment_created_mailer($noreplyuser);
        foreach ($learners as $learner) {
            $a->userfullname = fullname($learner);
            $mailer->mail($user, $a);
        }
    }

    /**
     * New user created.
     *
     * @param \local_licensing\event\user_created $event
     *
     * @return void
     */
    public static function user_created($event) {
        $site = get_site();
        $user = core_user::get_user($event->objectid);

        $loginurl = url_generator::login();

        $a = (object) array(
            'loginurl'      => (string) $loginurl,
            'signoff'       => generate_email_signoff(),
            'sitefullname'  => $site->fullname,
            'siteshortname' => $site->shortname,
            'userfullname'  => fullname($user),
            'userpassword'  => $event->other['password'],
            'userusername'  => $event->other['username'],
        );

        $mailer = new user_created_mailer(core_user::get_noreply_user());

        $mailer->mail($user, $a);
    }
}
