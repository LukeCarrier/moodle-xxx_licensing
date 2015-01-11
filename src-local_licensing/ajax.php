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

use local_licensing\capabilities;
use local_licensing\exception\input_exception;
use local_licensing\factory\product_factory;
use local_licensing\factory\target_factory;

// Comment this line to see fatal errors
define('AJAX_SCRIPT', true);

require_once dirname(dirname(__DIR__)) . '/config.php';
require_once "{$CFG->libdir}/coursecatlib.php";

$PAGE->set_cacheable(false);
$PAGE->set_context(context_system::instance());
$PAGE->set_url(new moodle_url('/local/licensing/ajax.php'));

require_login();

$result = (object) array(
    'success'  => true,
    'error'    => '',
    'response' => new stdClass(),
);

$objecttype = required_param('objecttype', PARAM_ALPHA);

if ($objecttype === 'allocationproduct') {
    require_capability(capabilities::DISTRIBUTE, $PAGE->context);

    $productsetid = required_param('productsetid', PARAM_INT);

    $producttypes     = product_factory::get_list();
    $result->response = array();

    foreach ($producttypes as $producttype) {
        $typeclass = product_factory::get_class_name($producttype);

        $result->response[$producttype]
                = $typeclass::get_in_product_set($productsetid);
    }
} else {
    $ids  = optional_param('ids',  '', PARAM_TEXT);
    $term = optional_param('term', '', PARAM_TEXT);

    $ids  = strlen($ids)  ? explode(',', $ids) : null;
    $term = strlen($term) ? $term              : null;

    if (($ids !== null && $term !== null)
            || ($ids === null && $term === null)) {
        throw new input_exception();
    }

    switch ($objecttype) {
        case 'product':
            $type = required_param('type', PARAM_ALPHA);
            require_capability(capabilities::ALLOCATE, $PAGE->context);

            $typeclass = product_factory::get_class_name($type);

            break;

        case 'target':
            $type = required_param('type', PARAM_ALPHA);
            require_capability(capabilities::MANAGE_TARGET_SETS, $PAGE->context);

            $typeclass = target_factory::get_class_name($type);

            break;

        case 'user':
            require_capability(capabilities::DISTRIBUTE, $PAGE->context);

            $typeclass = 'local_licensing\user_search_helper';

            break;

        default:
            throw new moodle_exception();
    }

    $result->response = $ids !== null ? $typeclass::get($ids)
                                      : $typeclass::search($term);
}

echo $OUTPUT->header(),
     json_encode($result),
     $OUTPUT->footer();
