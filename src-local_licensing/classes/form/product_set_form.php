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

use local_licensing\product_selector_dialogue;
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

        $this->product_selector_dialogue('course');
        $this->product_selector_dialogue('program');

        $this->add_action_buttons();
    }

    /**
     * Add a product selector dialogue to the form.
     *
     * Do not place multiple dialogs for the same product type in the same form.
     * We don't make any effort here to generate unique class names for the
     * dialogue, so weird things are likely to happen on calls to render().
     *
     * @param string $type The type of the product to render the selector widget
     *                     for.
     *
     * @return void
     */
    protected function product_selector_dialogue($type) {
        $default = $this->_customdata['record']->{"products{$type}"};

        product_selector_dialogue::add_form_field($this->_form, $type,
                                                  $default);
    }
}
