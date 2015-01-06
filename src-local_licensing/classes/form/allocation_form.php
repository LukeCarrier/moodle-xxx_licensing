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

use local_licensing\util;
use moodleform;

defined('MOODLE_INTERNAL') || die;

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

        $mform->addElement('select', 'targetid',
                           util::string('allocation:target'));
        $mform->setDefault('targetid', $data->targetid);
        $mform->setType('targetid', PARAM_INT);

        $mform->addElement('select', 'productsetid',
                           util::string('allocation:productset'));
        $mform->setDefault('productsetid', $data->productsetid);
        $mform->setType('productsetid', PARAM_INT);

        $mform->addElement('text', 'count', util::string('allocation:count'));
        $mform->setDefault('count', $data->count);
        $mform->setType('count', PARAM_INT);

        $this->add_action_buttons();
    }
}
