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

use csv_import_reader;
use dml_missing_record_exception;
use local_licensing\factory\target_factory;
use local_licensing\model\licence;
use stdClass;

defined('MOODLE_INTERNAL') || die;

require_once "{$CFG->libdir}/csvlib.class.php";

class user_csv_importer {
    /**
     * CSV import type.
     *
     * @var string
     */
    const CSV_IMPORT_TYPE = 'local_licensing_usercsv';

    /**
     * Allocation.
     *
     * @var \local_licensing\model\allocation
     */
    protected $allocation;

    /**
     * Column map.
     *
     * A map of column indexes to column names.
     *
     * @var string[]
     */
    protected $columns;

    /**
     * Line count.
     *
     * On a successful upload, this will contain a positive integer indicating
     * the number of lines to import. It can also be:
     *
     * -> null, if the attempt to parse the CSV file failed.
     * -> 0, if just the headers were supplied.
     *
     * @var integer
     */
    protected $count;

    /**
     * The distribution users are being imported into.
     *
     * @var \local_licensing\model\distribution
     */
    protected $distribution;

    /**
     * The unique ID for our import run.
     *
     * @var integer
     */
    protected $importid;

    /**
     * The number of lines we've processed.
     *
     * @var integer
     */
    protected $processed;

    /**
     * CSV import reader.
     *
     * @var \csv_import_reader
     */
    protected $reader;

    /**
     * Target.
     *
     * @var \local_licensing\model\target
     */
    protected $target;

    /**
     * Target set.
     *
     * @var \local_licensing\model\target_set
     */
    protected $targetset;

    /**
     * Initialiser.
     *
     * @param \local_licensing\model\distribution $distribution The distribution
     *                                                          users are being
     *                                                          imported into.
     * @param string                              $filecontents The contents of
     *                                                          the CSV file to
     *                                                          import, in
     *                                                          string format.
     */
    public function __construct($distribution, $filecontents) {
        $this->distribution = $distribution;

        $this->allocation = $this->distribution->get_allocation();
        $this->targetset  = $this->allocation->get_target_set();

        $this->importid = csv_import_reader::get_new_iid(static::CSV_IMPORT_TYPE);
        $this->reader   = new csv_import_reader($this->importid,
                                                static::CSV_IMPORT_TYPE);

        $this->count = $this->reader->load_csv_content($filecontents, 'utf-8',
                                                       ',');
        if ($this->count === false || $this->count == 0) {
            $this->reader->get_error();
        }

        $this->columns = $this->reader->get_columns();
        // TODO: validate columns

        $this->processed = 0;
    }

    /**
     * Execute the importer.
     *
     * Imports each user and creates each licence, line by line.
     */
    public function execute() {
        $this->reader->init();

        $target      = target_factory::for_user($this->distribution->createdby);
        $targetclass = $target->get_target_class();

        while ($line = $this->reader->next()) {
            $data = $this->parse_line($line);

            $data->idnumber
                    = $this->targetset->format_user_id_number($data->idnumber);

            try {
                $user = $this->get_user($data->idnumber);
                if ($this->update_user($user, $data)) {
                    user_helper::update($user);
                }
            } catch (dml_missing_record_exception $e) {
                $user = user_helper::create($data->firstname, $data->lastname,
                                            $data->username, $data->password,
                                            $data->email, $data->idnumber);
                $targetclass::assign_user($target->itemid, $user->id,
                                          $distribution->createdby);
            }

            $licence = new licence($this->distribution->id, $user->id);
            $licence->save();

            $this->processed++;
        }
    }

    /**
     * Retrieve a user by their ID number.
     *
     * @return \stdClass A DML object representing a user record.
     *
     * @throws \dml_missing_record_exception If no user record matches.
     */
    protected function get_user($idnumber) {
        global $DB;

        return $DB->get_record('user', array('idnumber' => $idnumber), '*',
                               MUST_EXIST);
    }

    /**
     * Extract data from a line array.
     *
     * Accessing all of our data using numerical keys is ugly and error prone,
     * so we'll just piece together an array from the line values and column map
     * we obtained earlier.
     *
     * @param mixed[] $line An array containing the values extracted from an
     *                      individual line.
     *
     * @return \stdClass An object, where each property is identified with the
     *                   name of the corresponding field.
     */
    protected function parse_line($line) {
        $data = new stdClass();

        foreach ($this->columns as $index => $name) {
            $data->{$name} = $line[$index];
        }

        return $data;
    }

    /**
     * Update the supplied user record with data in the supplied object.
     *
     * @param \stdClass $user
     * @param \stdClass $data
     *
     * @return boolean True if data was changed and the record requires saving,
     *                 else false.
     */
    protected function update_user($user, $data) {
        $fields = array(
            'email',
            'firstname',
            'idnumber',
            'lastname',
            'username',
        );

        $changed = false;

        foreach ($fields as $field) {
            if ($user->{$field} !== $data->{$field}) {
                $user->{$field} = $data->{$field};
                $changed = true;
            }
        }

        return $changed;
    }
}
