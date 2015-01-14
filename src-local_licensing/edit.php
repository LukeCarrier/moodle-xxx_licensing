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

use local_licensing\capabilities;
use local_licensing\exception\form_submission_exception;
use local_licensing\factory\form_factory;
use local_licensing\factory\model_factory;
use local_licensing\factory\product_factory;
use local_licensing\model\product;
use local_licensing\url_generator;
use local_licensing\util;

require_once dirname(dirname(__DIR__)) . '/config.php';
require_once "{$CFG->libdir}/adminlib.php";

$tabs = array('allocation', 'distribution', 'product_set', 'target_set');

$id  = optional_param('id',  0, PARAM_INT);
$tab = required_param('tab',    PARAM_ALPHAEXT);

if (!in_array($tab, $tabs)) {
    print_error('error:invalidtab', 'local_licensing');
}

$PAGE->set_context(context_system::instance());
$PAGE->set_url($ME);

require_login();
capabilities::require_for_tab($tab, $PAGE->context);

$tablangstring = str_replace('_', '', $tab);
$actionstring  = ($id === 0) ? 'create' : 'edit';

$record = model_factory::instance($tab, ($id > 0) ? $id : null);

$mformaction = url_generator::edit_url($tab, $id);
$redirecturl = url_generator::list_url($tab);
$mform       = form_factory::instance($tab, $mformaction->out(false),
                                      $record->model_to_form());

$adminrenderer   = $PAGE->get_renderer('admin');
$renderer        = $PAGE->get_renderer('local_licensing');
$submissionerror = null;

util::init_requirements();

if ($mform->is_cancelled()) {
    redirect($redirecturl);
} elseif ($data = $mform->get_data()) {
    try {
        $mform->save();
        redirect($redirecturl);
    } catch (form_submission_exception $submissionerror) {
        // We'll display a message above the edit form -- see below
    }
}

echo $OUTPUT->header(),
     $OUTPUT->heading(util::string('licensing')),
     $renderer->tabs($tab),
     $renderer->back_to_all($tab, $tablangstring),
     $OUTPUT->heading(util::string("{$tablangstring}:{$actionstring}"), 3);

if ($submissionerror !== null) {
    $adminrenderer->warning($submissionerror->getMessage());
}

$mform->display();

echo $OUTPUT->footer();
