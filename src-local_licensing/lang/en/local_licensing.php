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

defined('MOODLE_INTERNAL') || die;

// Module metadata
$string['pluginname'] = 'Licensing';

// Factory exceptions
$string['factory:incompleteimplementation'] = 'Attempt to use factory \'{$a}\' which is incomplete';

// Model exceptions
$string['model:incompleteimplementation'] = 'Attempt to use model \'{$a}\' which is incomplete';
$string['model:nosuchmethod']             = 'Attempt to call nonexistent method "$a"';

// Product exceptions
$string['product:incompleteimplementation'] = 'Attempt to use product \'{$a}\' which is incomplete';

// Shared
$string['licensing'] = 'Licensing';
$string['overview']  = 'Overview';

// Errors
$string['error:invalidtab'] = 'Please select a tab from the menu above.';

// Allocation
$string['allocation']            = 'Allocation';
$string['allocation:backtoall']  = 'Back to all allocations';
$string['allocation:create']     = 'Allocate licenses for a product set';
$string['allocation:count']      = 'Licence count';
$string['allocation:productset'] = 'Product set';
$string['allocation:target']     = 'Target';
$string['addallocation']         = 'Add allocation';

// Distribution
$string['distribution'] = 'Distribution';

// Product sets
$string['addproductset']        = 'Add product set';
$string['product']              = 'Product';
$string['products']             = 'Products';
$string['productset']           = 'Product set';
$string['productsets']          = 'Product sets';
$string['productset:backtoall'] = 'Back to all product sets';
$string['productset:create']    = 'Create a product set';
$string['productset:edit']      = 'Edit a product set';
$string['productset:name']      = 'Name';

// Products
$string['productset:products:course']  = 'Courses';
$string['productset:products:program'] = 'Programs';

// Product selector dialogue
$string['addxs'] = 'Add {$a}';

// Exceptions
$string['exception:input']                    = 'Invalid input';
$string['exception:incompleteimplementation'] = 'Incomplete implementation';
