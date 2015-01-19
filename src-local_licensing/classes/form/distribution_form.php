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

use local_licensing\capabilities;
use local_licensing\chooser_dialogue\user_chooser_dialogue;
use local_licensing\exception\form_submission_exception;
use local_licensing\exception\missing_target_exception;
use local_licensing\factory\target_factory;
use local_licensing\file\distribution_user_csv_file;
use local_licensing\model\allocation;
use local_licensing\model\distribution;
use local_licensing\model\licence;
use local_licensing\model\product;
use local_licensing\model\target_set;
use local_licensing\url_generator;
use local_licensing\util;
use moodleform;
use moodle_url;
use stdClass;

defined('MOODLE_INTERNAL') || die;

require_once "{$CFG->libdir}/formslib.php";

/**
 * Distribution form.
 */
class distribution_form extends moodleform {
    /**
     * User CSV field.
     *
     * @var string
     */
    const FIELD_USER_CSV = 'usercsv';

    /**
     * @override \moodleform
     */
    public function definition() {
        global $USER;

        $data  = $this->_customdata['record'];
        $mform = $this->_form;

        $context    = distribution_user_csv_file::get_context();
        $isbulk     = optional_param('bulk', false, PARAM_BOOL);
        $iscreation = $data->id == 0;

        try {
            $target      = target_factory::for_user($USER->id);
            $targetsetid = $target->targetsetid;
        } catch (missing_target_exception $e) {
            require_capability(capabilities::ALLOCATE, $context);
            $targetsetid = required_param('targetsetid', PARAM_INT);
        }

        $targetset = target_set::get_by_id($targetsetid);

        $mform->addElement('hidden', 'targetsetid', $targetset->id);
        $mform->setType('targetsetid', PARAM_INT);

        $mform->addElement('hidden', 'id', $data->id);
        $mform->setType('id', PARAM_INT);

        if ($isbulk) {
            $mform->addElement('filemanager', static::FIELD_USER_CSV,
                               util::string('distribution:usercsv'), null,
                               distribution_user_csv_file::get_options());

            $draftitemid = file_get_submitted_draft_itemid(static::FIELD_USER_CSV);
            file_prepare_draft_area($draftitemid, $context->id,
                                    distribution_user_csv_file::get_component(),
                                    distribution_user_csv_file::get_file_area(),
                                    $data->id,
                                    distribution_user_csv_file::get_options());

            $mform->setDefault(static::FIELD_USER_CSV, $draftitemid);
        } else {
            $bulksubs = new stdClass();
            $bulksubs->url = new moodle_url($mform->getAttribute('action'));
            $bulksubs->url->param('bulk', true);
            if ($iscreation) {
                $bulksubs->url->remove_params(array('id'));
            }
            if (!isset($target)) {
                $bulksubs->url->param('targetsetid', $targetset->id);
            }

            $mform->addElement('static', 'usercsvstatic',
                               util::string('bulkupload'),
                               util::paragraphs('bulkuploaddesc', 2, 1,
                                                $bulksubs));
        }

        $mform->addElement('select', 'allocationid',
                           util::string('distribution:allocation'),
                           allocation::menu_for_target_set($targetset->id));
        $mform->setDefault('allocationid', $data->allocationid);
        $mform->setType('allocationid', PARAM_INT);

        $productstring = util::string('distribution:product');
        if ($iscreation) {
            $mform->addElement('select', 'productid',
                               $productstring);
            $mform->setDefault('productid', $data->productid);
            $mform->setType('productid', PARAM_INT);
            $this->update_select_options('allocationid', 'productid');
        } else {
            $mform->addElement('static', 'productid', $productstring,
                               product::get_by_id($data->productid)->get_name());
        }

        if (!$iscreation || ($iscreation && !$isbulk)) {
            $this->user_chooser_dialogue($iscreation, $targetset->id);
        }

        if ($iscreation) {
            $this->add_action_buttons();
        } else {
            $this->_form->hardFreeze();
        }
    }

    /**
     * Save the form values.
     *
     * @return void
     */
    public function save() {
        $data = $this->get_data();

        $isbulk = !!file_get_submitted_draft_itemid(static::FIELD_USER_CSV);

        /* Hack - get the selected product ID. This is required since Moodle's
         * form library will filter out values that weren't in the array of
         * values passed to addElement(). Since we add options to this form on
         * the client, we'll have to work around this requirement. */
        $productdetails = required_param('productid', PARAM_TEXT);
        list($producttype, $productid) = explode('-', $productdetails, 2);

        $iscreation = $data->id == 0;

        if ($iscreation) {
            $allocation = allocation::get_by_id($data->allocationid);

            // Deduce the product ID from the product and allocation details
            $product = product::get(array(
                'productsetid' => $allocation->productsetid,
                'type'         => $producttype,
                'itemid'       => $productid,
            ));
            $data->productid = $product->id;

            if (!$isbulk) {
                if ($data->user === '') {
                    throw new form_submission_exception('noselectedusers');
                }
                $userids = explode(',', $data->user);

                $required  = count($userids);
                $available = $allocation->get_available();

                if ($required > $available) {
                    $subs = (object) array(
                        'available' => $available,
                        'required'  => $required,
                    );
                    throw new form_submission_exception('insufficientlicences',
                                                        $subs);
                }
            }
        }

        $distribution = distribution::model_from_form($data);
        $distribution->id = ($iscreation) ? null : $data->id;
        $distribution->save();

        if ($iscreation) {
            if ($isbulk) {
                $context     = distribution_user_csv_file::get_context();
                $draftitemid = file_get_submitted_draft_itemid(static::FIELD_USER_CSV);

                file_save_draft_area_files($draftitemid, $context->id,
                                           distribution_user_csv_file::get_component(),
                                           distribution_user_csv_file::get_file_area(),
                                           $distribution->id,
                                           distribution_user_csv_file::get_options());
            } else {
                foreach ($userids as $userid) {
                    $licence = new licence($distribution->id, $userid);
                    $licence->save();
                }
            }
        }
    }

    /**
     * Update the options for a select field based on value changes of another.
     *
     * @param string $source The name of the source field.
     * @param string $target The name of the target field.
     *
     * @return void
     */
    protected function update_select_options($source, $target) {
        global $PAGE;

        $ajaxurl = url_generator::ajax();

        $PAGE->requires->yui_module('moodle-local_licensing-updateselectoptions',
                                    'M.local_licensing.init_update_select_options',
                                    array(array(
            'ajaxurl' => $ajaxurl->out_as_local_url(),
            'param'   => 'allocationid',
            'params'  => array(
                'action'     => 'search',
                'objecttype' => 'allocationproduct',
            ),
            'source'  => "#id_{$source}",
            'target'  => "#id_{$target}",
        )));
    }

    /**
     * Add a user chooser dialogue to the form.
     *
     * @param string  $type        The type of the product to render the
     *                             chooser dialogue for.
     * @param integer $targetsetid The ID of the target set users must belong
     *                             to. Only applied if editable.
     *
     * @return void
     */
    protected function user_chooser_dialogue($editable, $targetsetid=null) {
        $default = $this->_customdata['record']->users;

        if ($editable) {
            $extraparams = array('targetsetid' => $targetsetid);
            user_chooser_dialogue::add_form_field($this->_form, $default, null,
                                                  $extraparams);
        } else {
            user_chooser_dialogue::add_static_list($this->_form, $default);
        }
    }
}
