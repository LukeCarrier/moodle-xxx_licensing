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

use local_licensing\exception\input_exception;
use local_licensing\factory\product_factory;

define('AJAX_SCRIPT', true);

require_once dirname(dirname(__DIR__)) . '/config.php';
require_once "{$CFG->libdir}/coursecatlib.php";

$PAGE->set_cacheable(false);
$PAGE->set_context(context_system::instance());
$PAGE->set_url(new moodle_url('/local/licensing/ajax.php'));

require_login();

$type = required_param('type', PARAM_ALPHA);

$result = (object) array(
    'success'  => true,
    'error'    => '',
    'response' => new stdClass(),
);

switch ($type) {
    case 'product':
        require_capability('local/licensing:allocatelicenses', $PAGE->context);

        $ids  = optional_param('ids',  '', PARAM_TEXT);
        $term = optional_param('term', '', PARAM_TEXT);

        $ids  = strlen($ids)  ? explode(',', $ids) : null;
        $term = strlen($term) ? $term              : null;

        if (($ids !== null && $term !== null)
                || ($ids === null && $term === null)) {
            throw new input_exception();
        }

        $producttype = required_param('producttype', PARAM_ALPHA);
        $typeclass   = product_factory::get_class_name($producttype);

        $result->response = $ids !== null ? $typeclass::get($ids)
                                          : $typeclass::search($term);

        break;

    case 'user':
        require_capability('local/licensing:distributelicenses', $PAGE->context);

        // todo

        break;

    default:
        throw new moodle_exception();
}

echo $OUTPUT->header(),
     json_encode($result),
     $OUTPUT->footer();
