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
$string['bulkupload']      = 'Bulk upload';
$string['bulkuploaddesc1'] = 'If you are distributing licences to multiple users, it may be quicker to follow the <a href="{$a->url}">bulk upload process</a>.';
$string['bulkuploaddesc2'] = 'If you wish to distribute licences manually, please proceed using the form below.';
$string['licensing']       = 'Licensing';
$string['overview']        = 'Overview';

// Errors
$string['error:invalidtab'] = 'Please select a tab from the menu above.';

// Allocation
$string['addallocation']         = 'Add allocation';
$string['allocation']            = 'Allocation';
$string['allocations']           = 'Allocations';
$string['allocation:availablex'] = 'Available: {$a}';
$string['allocation:backtoall']  = 'Back to all allocations';
$string['allocation:consumedx']  = 'Consumed: {$a}';
$string['allocation:count']      = 'Count';
$string['allocation:counts']     = 'Counts';
$string['allocation:countx']     = 'Total: {$a}';
$string['allocation:create']     = 'Allocate licences for a product set';
$string['allocation:edit']       = 'Edit an allocation for a product set';
$string['allocation:enddate']    = 'End date';
$string['allocation:name']       = '{$a->productsetname} ({$a->available} of {$a->count} available)';
$string['allocation:none']       = 'none';
$string['allocation:productset'] = 'Product set';
$string['allocation:progress']   = 'Progress';
$string['allocation:startdate']  = 'Start date';
$string['allocation:targetset']  = 'Target set';

// Distribution
$string['adddistribution']                = 'Add distribution';
$string['distribution']                   = 'Distribution';
$string['distributions']                  = 'Distributions';
$string['distribution:backtoall']         = 'Back to all distributions';
$string['distribution:create']            = 'Distribute licences to users';
$string['distribution:edit']              = 'Edit an existing distribution';
$string['distribution:allocation']        = 'Allocation';
$string['distribution:count']             = 'Count';
$string['distribution:count:bulkpending'] = 'Pending';
$string['distribution:product']           = 'Product';
$string['distribution:productset']        = 'Product set';
$string['distribution:usercsv']           = 'CSV file';
$string['distribution:users']             = 'Users';

// Distribution exceptions
$string['exception:formsubmission:insufficientlicences'] = 'The selected allocation has {$a->available} licences, but {$a->users} were selected for enrolment.';
$string['exception:formsubmission:noselectedusers']      = 'No users have been selected for distribution.';

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

// Targets
$string['addtargetset']                 = 'Add target set';
$string['targetset']                    = 'Target set';
$string['targetsets']                   = 'Target sets';
$string['targetset:backtoall']          = 'Back to all target sets';
$string['targetset:create']             = 'Create a target set';
$string['targetset:edit']               = 'Edit a target set';
$string['targetset:name']               = 'Name';
$string['targetset:targets']            = 'Targets';
$string['targetset:useridnumberformat'] = 'User ID number format string';

// Target set exceptions
$string['exception:formsubmission:invaliduseridformatstring'] = 'The supplied format string does not contain "%s".';

// Products
$string['productset:products:course']  = 'Courses';
$string['productset:products:program'] = 'Programs';

// Targets
$string['targetset:target:organisation'] = 'Organisation';

// Product selector dialogue
$string['addxs'] = 'Add {$a}';

// Cron exceptions
$string['exception:croncollision'] = 'Another instance of the licensing cron appears to be running. Running multiple instances of the cron at a time will likely lead to unexpected behaviour and is not supported.';

// Exceptions
$string['exception:formsubmission']           = 'Errors were encountered when processing the submission.';
$string['exception:input']                    = 'Invalid input';
$string['exception:incompleteimplementation'] = 'Incomplete implementation';
$string['exception:missingtarget']            = 'This user does not appear to be a member of any targets';

// Capabilities
$string['licensing:allocatelicences']   = 'Allocate licences to target sets';
$string['licensing:distributelicences'] = 'Distribute licences to learners';
$string['licensing:manageproductsets']  = 'Manage product sets for allocation';
$string['licensing:managetargetsets']   = 'Manage target sets for allocation';

// Events
$string['event:allocationcreated:desc'] = 'Allocation {$a->objectid} created by {a->userid}';
