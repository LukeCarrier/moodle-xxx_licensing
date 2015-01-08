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

namespace local_licensing\form;

use local_licensing\chooser_dialogue\product_chooser_dialogue;
use local_licensing\factory\product_factory;
use local_licensing\model\product;
use local_licensing\model\product_set;
use local_licensing\util;
use moodleform;

defined('MOODLE_INTERNAL') || die;

/**
 * Product set form.
 */
class product_set_form extends moodleform {
    /**
     * @override \moodleform
     */
    public function definition() {
        $data  = $this->_customdata['record'];
        $mform = $this->_form;

        $mform->addElement('hidden', 'id', $data->id);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('text', 'name', util::string('productset:name'));
        $mform->setDefault('name', $data->name);
        $mform->setType('name', PARAM_TEXT);

        $producttypes = product_factory::get_list();
        foreach ($producttypes as $producttype) {
            $this->product_chooser_dialogue($producttype);
        }

        $this->add_action_buttons();
    }

    /**
     * Add a product chooser dialogue to the form.
     *
     * Do not place multiple dialogs for the same product type in the same form.
     * We don't make any effort here to generate unique class names for the
     * dialogue, so weird things are likely to happen on calls to render().
     *
     * @param string $type The type of the product to render the chooser
     *                     dialogue for.
     *
     * @return void
     */
    protected function product_chooser_dialogue($type) {
        $default = $this->_customdata['record']->{"products{$type}"};

        product_chooser_dialogue::add_form_field($this->_form, $type,
                                                 $default);
    }

    /**
     * Save the form values.
     *
     * @return void
     */
    public function save() {
        $data = $this->get_data();

        $productset = product_set::model_from_form($data);
        $productset->id = ($data->id == 0) ? null : $data->id;
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
    }
}
