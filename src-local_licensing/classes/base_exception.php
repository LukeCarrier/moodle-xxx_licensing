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

use moodle_exception;

defined('MOODLE_INTERNAL') || die;

/**
 * Base exception.
 */
class base_exception extends moodle_exception {
    /**
     * Moodle module.
     *
     * @var string
     */
    const MOODLE_MODULE = 'local_licensing';

    /**
     * Initialiser.
     *
     * @param string $prefix The exception's language string prefix.
     * @param string $code   An optional specific error code, appended to the
     *                       prefix to obtain a language string.
     * @param mixed  $a      Optional substitions to make with placeholders in
     *                       the language string.
     */
    public function __construct($prefix, $code=null, $a=null) {
        $string = ($code === null) ? $prefix : "{$prefix}:{$code}";

        parent::__construct($string, static::MOODLE_MODULE, $a);
    }
}
