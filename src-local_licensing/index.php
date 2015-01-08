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
use local_licensing\model\product_set;
use local_licensing\url_generator;
use local_licensing\util;

require_once dirname(dirname(__DIR__)) . '/config.php';
require_once "{$CFG->libdir}/adminlib.php";

admin_externalpage_setup('local_licensing');

$tab = optional_param('tab', 'overview', PARAM_ALPHAEXT);

$renderer = $PAGE->get_renderer('local_licensing');

$PAGE->set_context(context_system::instance());
util::init_requirements();

echo $OUTPUT->header(),
     $OUTPUT->heading(util::string('licensing')),
     $renderer->tabs($tab);

switch ($tab) {
    case 'allocation':
        require_capability(capabilities::ALLOCATE, $PAGE->context);
        echo $OUTPUT->heading(util::string('allocation'), 3),
             $renderer->allocation_table(allocation::all()),
             $OUTPUT->single_button(url_generator::add_allocation(),
                                    util::string('addallocation'),
                                    'get');
        break;

    case 'distribution':
        require_capability(capabilities::DISTRIBUTE, $PAGE->context);
        echo $OUTPUT->heading(util::string('distribution'), 3),
             $renderer->distribution_table(distribution::all()),
             $OUTPUT->single_button(url_generator::add_distribution(),
                                    util::string('adddistribution'),
                                    'get');
        break;

    case 'overview':
        echo $OUTPUT->heading(util::string('overview'), 3),
             "We'll need to put some stats in here later";
        break;

    case 'product_set':
        require_capability(capabilities::MANAGE_PRODUCT_SETS, $PAGE->context);
        echo $OUTPUT->heading(util::string('productsets'), 3),
             $renderer->product_set_table(product_set::all()),
             $OUTPUT->single_button(url_generator::add_product_set(),
                                    util::string('addproductset'),
                                    'get');
        break;

    default:
        print_error('error:invalidtab', util::MOODLE_MODULE);
}

echo $OUTPUT->footer();
