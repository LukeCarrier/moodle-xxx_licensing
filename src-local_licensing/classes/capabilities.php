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

use local_licensing\exception\input_exception;

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
     * @var string
     */
    const MANAGE_TARGET_SETS = 'local/licensing:managetargetsets';

    /**
     * Get all capability names.
     *
     * @return string[] All capability names.
     */
    public static function all() {
        return array(
            capabilities::ALLOCATE,
            capabilities::DISTRIBUTE,
            capabilities::MANAGE_PRODUCT_SETS,
            capabilities::MANAGE_TARGET_SETS,
        );
    }

    /**
     * Get the capability required for accessing a specific tab.
     *
     * @param string $tab The tab being accessed.
     *
     * @return string The name of the corresponding capability.
     */
    public static function for_tab($tab) {
        switch ($tab) {
            case 'allocation':
                $capability = static::ALLOCATE;
                break;

            case 'distribution':
                $capability = static::DISTRIBUTE;
                break;

            case 'product_set':
                $capability = static::MANAGE_PRODUCT_SETS;
                break;

            case 'target_set':
                $capability = static::MANAGE_TARGET_SETS;
                break;

            default:
                throw new input_exception();
        }

        return $capability;
    }

    /**
     * Require the capability for the active tab.
     *
     * @param string   $tab     The tab being accessed.
     * @param \context $context The context in which to check for the
     *                          capability.
     *
     * @return void
     */
    public static function require_for_tab($tab, $context) {
        if ($tab !== 'overview') {
            require_capability(static::for_tab($tab), $context);
        }
    }
}
