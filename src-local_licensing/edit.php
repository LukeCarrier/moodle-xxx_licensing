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

use local_licensing\factory\form_factory;
use local_licensing\factory\model_factory;
use local_licensing\factory\product_factory;
use local_licensing\model\product;
use local_licensing\url_generator;
use local_licensing\util;

require_once dirname(dirname(__DIR__)) . '/config.php';
require_once "{$CFG->libdir}/adminlib.php";

admin_externalpage_setup('local_licensing');

$tabs = array('allocation', 'distribution', 'product_set', 'target_set');

$id  = optional_param('id',  0, PARAM_INT);
$tab = required_param('tab',    PARAM_ALPHAEXT);

if (!in_array($tab, $tabs)) {
    print_error('error:invalidtab', 'local_licensing');
}

$tablangstring = str_replace('_', '', $tab);
$actionstring  = ($id === 0) ? 'create' : 'edit';

$record = model_factory::instance($tab, ($id > 0) ? $id : null);

$mformaction = url_generator::edit_url($tab, $id);
$redirecturl = url_generator::list_url($tab);
$mform       = form_factory::instance($tab, $mformaction->out(false),
                                      $record->model_to_form());

$renderer = $PAGE->get_renderer('local_licensing');

$context       = context_system::instance();
$canallocate   = 'local/licensing:allocatelicenses';
$candistribute = 'local/licensing:distributelicenses';

util::init_requirements();

if ($mform->is_cancelled()) {
    redirect($redirecturl);
} elseif ($data = $mform->get_data()) {
    $productset = model_factory::instance_from_form($tab, $data);
    $productset->id = ($id === 0) ? null : $id;
    $productset->save();

    $formproducts = array();
    foreach (product_factory::get_list() as $type) {
        $value = $data->{"products{$type}"};
        $formproducts[$type] = ($value === '') ? array() : explode(',', $value);
    }

    $existingproducts = $productset->get_products();

    $deleteproducts = array();
    foreach ($existingproducts as $product) {
        if (!in_array($product->id, $formproducts[$product->type])) {
            $deleteproducts[] = $product;
        }
    }

    $createproducts = array();
    foreach ($formproducts as $type => $products) {
        foreach ($products as $productid) {
            $found = false;
            foreach ($existingproducts as $existingproduct) {
                if (!$found
                        && $existingproduct->type !== $type
                        && $existingproduct->itemid != $productid) {
                    $found = true;
                }
            }

            if (!$found) {
                $createproducts[] = new product($productset->id, $type,
                                                $productid);
            }
        }
    }

    foreach ($createproducts as $product) {
        $product->save();
    }

    foreach ($deleteproducts as $product) {
        $product->delete();
    }

    redirect($redirecturl);
} else {
    echo $OUTPUT->header(),
         $OUTPUT->heading(util::string('licensing')),
         $renderer->tabs($tab),
         $renderer->back_to_all($tab, $tablangstring),
         $OUTPUT->heading(util::string("{$tablangstring}:{$actionstring}"), 3);

    $mform->display();

    echo $OUTPUT->footer();
}
