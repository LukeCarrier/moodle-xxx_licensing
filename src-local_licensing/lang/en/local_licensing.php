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

// Allocation created message
$string['messageprovider:allocationcreated']         = 'New licence allocations';
$string['messageprovider:allocationcreated:full']    = '<p>Hello {$a->userfullname},</p>
<p>A new allocation of <strong>{$a->licencecount}</strong> licence(s) for <strong>{$a->productsetname}</strong> has been created and is now available for distribution.</p>
<p>{$a->signoff}</p>';
$string['messageprovider:allocationcreated:small']   = 'New allocation #{$a->id} of {$a->licencecount} licence(s) created for {$a->productsetname}';
$string['messageprovider:allocationcreated:subject'] = 'New licence allocation #{$a->id}';

// Distribution created message
$string['messageprovider:distributioncreated']         = 'New licence distributions';
$string['messageprovider:distributioncreated:full']    = '<p>Hello {$a->userfullname},</p>
<p>A new distribution of <strong>{$a->licencecount}</strong> licence(s) for <strong>{$a->productname}</strong> was created by {$a->createdbyfullname}.</p>
<p>The selected users are now being enrolled onto the the specified course content. You will receive another email once the user enrolment process is complete.</p>
<p>{$a->signoff}</p>';
$string['messageprovider:distributioncreated:small']   = 'New distribution #{$a->id} of {$a->licencecount} licence(s) for {$a->productname} created by {$a->createdbyfullname}';
$string['messageprovider:distributioncreated:subject'] = 'New licence distribution #{$a->id}';

// Distribution licences created message
$string['messageprovider:distributionlicencescreated:full']    = '<p>Hello {$a->userfullname},</p>
<p>The user enrolment process is now complete. The following users were enrolled:</p>
{$a->learnerlist}
<p>{$a->signoff}</p>';
$string['messageprovider:distributionlicencescreated:small']   = 'The user enrolment process for distribution #{$a->id} is now complete';
$string['messageprovider:distributionlicencescreated:subject'] = 'New licence distribution #{$a->id}';

// User created message
$string['messageprovider:usercreated']         = 'New user creations';
$string['messageprovider:usercreated:full']    = '<p>Hello {$a->userfullname},</p>
<p>Welcome to {$a->sitefullname}.</p>
<p>Your user account has now been created and your course content will be accessible to you very soon. We\'ll send you another email as soon as it\'s ready.</p>
<p>Your login details are as follows:</p>
<ul>
    <li>Login at <a href="{$a->loginurl}">{$a->loginurl}</a></li>
    <li>Username: {$a->userusername}</li>
    <li>Password: {$a->userpassword}</li>
</ul>
<p>{$a->signoff}</p>';
$string['messageprovider:usercreated:small']   = 'Your account at {$a->loginurl} has been created with username {$a->userusername} and password {$a->userpasword}';
$string['messageprovider:usercreated:subject'] = 'Welcome to {$a->siteshortname}, {$a->userfullname}';

// User enrolment created message
$string['messageprovider:userenrolmentcreated']         = 'New user enrolment creations';
$string['messageprovider:userenrolmentcreated:full']    = '<p>Hello {$a->userfullname},</p>
<p>Your account at {$a->sitefullname} has been enrolled in <strong>{$a->productname}</strong>.</p>
<p>Your enrolment will be active from <strong>{$a->allocationstartdate}</strong> and will expire on <strong>{$a->allocationenddate}</strong>. Please be sure to complete the content between these dates.</p>
<p>Visit <a href="{$a->loginurl}">{$a->loginurl}</a> to get started.</p>
<p>{$a->signoff}</p>';
$string['messageprovider:userenrolmentcreated:small']   = 'Your account at {$a->loginurl} will be enrolled on to {$a->productname} between {$a->allocationstartdate} and {$a->allocationenddate}';
$string['messageprovider:userenrolmentcreated:subject'] = 'New learning content available at {$a->siteshortname}, {$a->userfullname}';

// User CSV import failure
$string['messageprovider:usercsvimportfailure:full']    = '<p>Hi {$a->userfullname},</p>
<p>Our attempt to process your bulk user upload for your distribution failed with the following error:</p>
<ul>
    <li>Distribution ID: <code>{$a->id}</code></li>
    <li>Message: <code>{$a->message}</code></li>
    <li>Code: <code>{$a->errorcode}</code></li>
</ul>
<p>As the import failed, the distribution has been removed and no licences were deducted. Please ensure that the the CSV file you uploaded was well formed and try again.</p>
<p>{$a->signoff}</p>';
$string['messageprovider:usercsvimportfailure:small']   = 'Bulk upload of user data for distribution #{$a->id} failed with code {$a->errorcode}';
$string['messageprovider:usercsvimportfailure:subject'] = 'New licence distribution #{$a->id}';

// Cron exceptions
$string['exception:croncollision'] = 'Another instance of the licensing cron appears to be running. Running multiple instances of the cron at a time will likely lead to unexpected behaviour and is not supported.';

// Exceptions
$string['exception:formsubmission']           = 'Errors were encountered when processing the submission.';
$string['exception:input']                    = 'Invalid input';
$string['exception:incompleteimplementation'] = 'Incomplete implementation';
$string['exception:missingtarget']            = 'This user does not appear to be a member of any targets';

// Capabilities
$string['licensing:allocatelicences']         = 'Allocate licences to target sets';
$string['licensing:distributelicences']       = 'Distribute licences to learners';
$string['licensing:manageproductsets']        = 'Manage product sets for allocation';
$string['licensing:managetargetsets']         = 'Manage target sets for allocation';
$string['licensing:receiveenrolnotification'] = 'Receive enrolment notification';

// Events
$string['event:allocationcreated']                = 'New licence allocation created';
$string['event:allocationcreated:desc']           = 'Allocation {$a->objectid} created by {a->userid}';
$string['event:distributioncreated']              = 'New licence distribution created';
$string['event:distributioncreated:desc']         = 'Distribution {$a->objectid} created by {$a->userid}';
$string['event:distributionlicencescreated']      = 'All licences for a distribution created';
$string['event:distributionlicencescreated:desc'] = 'Licences for distribution {$a->objectid} created';
$string['event:usercreated']                      = 'New user created for a distribution';
$string['event:usercreated:desc']                 = 'User {$a->objectid} created';
$string['event:usercsvimportfailed']              = 'User CSV import failure';
$string['event:usercsvimportfailed:desc']         = 'Import of user data for distribution {$a->objectid} failed';
