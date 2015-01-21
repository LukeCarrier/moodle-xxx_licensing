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

namespace local_licensing\mailer;

use local_licensing\base_mailer;

defined('MOODLE_INTERNAL') || die;

class enrolment_created_mailer extends base_mailer {
    /**
     * @override \local_licensing\base_mailer
     */
    public function __construct($sender) {
        parent::__construct($sender,
                            'messageprovider:userenrolmentcreated:subject',
                            'messageprovider:userenrolmentcreated:small',
                            'messageprovider:userenrolmentcreated:full');
    }

    /**
     * @override \local_licensing\base_mailer
     */
    protected static function get_name() {
        return 'userenrolmentcreated';
    }
}
