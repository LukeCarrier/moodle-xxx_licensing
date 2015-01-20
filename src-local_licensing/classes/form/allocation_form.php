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

use context_system;
use local_licensing\event\allocation_created;
use local_licensing\model\allocation;
use local_licensing\model\target_set;
use local_licensing\model\product_set;
use local_licensing\util;
use moodleform;

defined('MOODLE_INTERNAL') || die;

require_once "{$CFG->libdir}/formslib.php";

/**
 * Allocation form.
 */
class allocation_form extends moodleform {
    /**
     * @override \moodleform
     */
    public function definition() {
        $data  = $this->_customdata['record'];
        $mform = $this->_form;

        $mform->addElement('hidden', 'id', $data->id);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('select', 'productsetid',
                           util::string('allocation:productset'),
                           product_set::menu());
        $mform->setDefault('productsetid', $data->productsetid);
        $mform->setType('productsetid', PARAM_INT);

        $mform->addElement('select', 'targetsetid',
                           util::string('allocation:targetset'),
                           target_set::menu());
        $mform->setDefault('targetsetid', $data->targetsetid);
        $mform->setType('targetsetid', PARAM_INT);

        $mform->addElement('text', 'count', util::string('allocation:count'));
        $mform->setDefault('count', $data->count);
        $mform->setType('count', PARAM_INT);

        foreach (array('start', 'end') as $field) {
            $name = "{$field}date";
            $time = ($field === 'start') ? time() : (time() + YEARSECS);

            $mform->addElement('date_selector', $name,
                               util::string("allocation:{$name}"));
            $mform->setDefault($name, time());
            $mform->setType($name, PARAM_INT);
        }

        $this->add_action_buttons();
    }

    /**
     * Save the form values.
     *
     * @return void
     */
    public function save() {
        $data       = $this->get_data();
        $iscreation = $data->id == 0;

        $allocation = allocation::model_from_form($data);
        $allocation->id = $iscreation ? null : $data->id;
        $allocation->save();

        if ($iscreation) {
            $event = allocation_created::instance($allocation,
                                                  context_system::instance());
            $event->trigger();
        }
    }
}
