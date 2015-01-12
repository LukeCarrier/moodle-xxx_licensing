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
use local_licensing\model\allocation;
use local_licensing\model\distribution;
use local_licensing\model\product_set;
use local_licensing\model\target_set;
use local_licensing\url_generator;
use local_licensing\util;

require_once dirname(dirname(__DIR__)) . '/config.php';
require_once "{$CFG->libdir}/adminlib.php";

$tab = optional_param('tab', 'overview', PARAM_ALPHAEXT);

$PAGE->set_context(context_system::instance());
$PAGE->set_url($ME);

$renderer = $PAGE->get_renderer('local_licensing');

require_login();
capabilities::require_for_tab($tab, $PAGE->context);

util::init_requirements();

echo $OUTPUT->header(),
     $OUTPUT->heading(util::string('licensing')),
     $renderer->tabs($tab);

switch ($tab) {
    case 'allocation':
        echo $OUTPUT->heading(util::string('allocation'), 3),
             $renderer->allocation_table(allocation::get_active_allocations()),
             $OUTPUT->single_button(url_generator::add_allocation(),
                                    util::string('addallocation'), 'get');
        break;

    case 'distribution':
        echo $OUTPUT->heading(util::string('distribution'), 3),
             $renderer->distribution_table(distribution::all()),
             $OUTPUT->single_button(url_generator::add_distribution(),
                                    util::string('adddistribution'), 'get');
        break;

    case 'overview':
        echo $OUTPUT->heading(util::string('overview'), 3),
             "We'll need to put some stats in here later";
        break;

    case 'product_set':
        echo $OUTPUT->heading(util::string('productsets'), 3),
             $renderer->product_set_table(product_set::all()),
             $OUTPUT->single_button(url_generator::add_product_set(),
                                    util::string('addproductset'), 'get');
        break;

    case 'target_set':
        echo $OUTPUT->heading(util::string('targetsets'), 3),
             $renderer->target_set_table(target_set::all()),
             $OUTPUT->single_button(url_generator::add_target_set(),
                                    util::string('addtargetset'), 'get');
        break;

    default:
        print_error('error:invalidtab', util::MOODLE_MODULE);
}

echo $OUTPUT->footer();
