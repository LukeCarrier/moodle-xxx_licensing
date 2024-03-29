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

use local_licensing\chooser_dialogue\target_chooser_dialogue;
use local_licensing\factory\target_factory;
use local_licensing\model\target_set;
use local_licensing\util;
use moodleform;

defined('MOODLE_INTERNAL') || die;

require_once "{$CFG->libdir}/formslib.php";

/**
 * Target form.
 */
class target_set_form extends moodleform {
    /**
     * @override \moodleform
     */
    public function definition() {
        $data  = $this->_customdata['record'];
        $mform = $this->_form;

        $mform->addElement('hidden', 'id', $data->id);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('text', 'name', util::string('targetset:name'));
        $mform->setDefault('name', $data->name);
        $mform->setType('name', PARAM_TEXT);

        $mform->addElement('text', 'useridnumberformat',
                           util::string('targetset:useridnumberformat'));
        $mform->setDefault('useridnumberformat',
                ($data->useridnumberformat) ? $data->useridnumberformat : '%s');
        $mform->setType('useridnumberformat', PARAM_TEXT);

        $targettypes = target_factory::get_list();
        foreach ($targettypes as $targettype) {
            $this->target_chooser_dialogue($targettype);
        }

        $this->add_action_buttons();
    }

    /**
     * Save the form values.
     *
     * @return void
     */
    public function save() {
        $data = $this->get_data();

        if (strpos($data->useridnumberformat, '%s') === false) {
            throw new form_submission_exception('invaliduseridformatstring');
        }

        $targetset = target_set::model_from_form($data);
        $targetset->id = ($data->id == 0) ? null : $data->id;
        $targetset->save();

        $existingtargets = $targetset->get_targets();
        list($createtargets, $deletetargets)
                = util::delta_set('target', 'local_licensing\model\target',
                                  target_factory::get_list(),
                                  $existingtargets, $targetset->id, $data);

        foreach ($createtargets as $target) {
            $target->save();
        }

        foreach ($deletetargets as $target) {
            $target->delete();
        }
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
    protected function target_chooser_dialogue($type) {
        $default = $this->_customdata['record']->{"targets{$type}"};

        target_chooser_dialogue::add_form_field($this->_form, $default, $type);
    }
}
