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

namespace local_licensing;

/**
 * All of our capabilities.
 *
 * Throwing strings around seems a little trashy. Constants are enterprise
 * ready.
 */
class capabilities {
    /**
     * Can allocate licenses to targets.
     *
     * @var string
     */
    const ALLOCATE = 'local/licensing:allocatelicenses';

    /**
     * Can distribute licenses to users within a target.
     *
     * @var string
     */
    const DISTRIBUTE = 'local/licensing:distributelicenses';

    /**
     * Can manage product sets.
     *
     * @var string
     */
    const MANAGE_PRODUCT_SETS = 'local/licensing:manageproductsets';

    /**
     * Can manage target sets.
     *
     * @var string\
     */
    const MANAGE_TARGET_SETS = 'local/licensing:managetargetsets';
}
