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

defined('MOODLE_INTERNAL') || die;

/**
 * Base mailer class.
 *
 * Mailers send messages via the Moodle messaging API. They hide the arcane
 * logic that has to go into message providers.
 */
class base_mailer {
    /**
     * Moodle component name.
     *
     * @var string
     */
    const MOODLE_MODULE = 'local_licensing';

    /**
     * Name of the Moodle string used for the full message.
     *
     * @var string
     */
    protected $fullmessagehtmlstring;

    /**
     * Basic object with properties defined for message_send().
     *
     * @var \stdClass
     */
    protected $message;

    /**
     * Name of the Moodle string used for the message subject.
     *
     * @var string
     */
    protected $subjectstring;

    /**
     * Name of the Moodle string used for the small message.
     *
     * @var string
     */
    protected $smallmessagestring;

    /**
     * Strings we need to update per-message.
     *
     * @var string[]
     */
    protected static $strings = array(
        'subject',
        'smallmessage',
        'fullmessagehtml',
    );

    /**
     * Initialiser.
     *
     * @param \stdClass $sender User object.
     */
    public function __construct($sender, $subjectstring, $smallmessagestring,
                                $fullmessagehtmlstring) {
        $this->message = (object) array( 
            'component' => static::MOODLE_MODULE,
            'name'      => static::get_name(),

            'userfrom' => $sender,
            'userto'   => null,

            'subject'           => null,
            'smallmessage'      => null,
            'fullmessage'       => null,
            'fullmessagehtml'   => null,
            'fullmessageformat' => FORMAT_HTML,
        );

        foreach (static::$strings as $string) {
            $property = "{$string}string";

            $this->{$property} = $$property;
        }
    }

    /**
     * Get the name of the message provider.
     *
     * @return string The name of the message provider, as defined in
     *                /db/messages.php.
     */
    protected static function get_name() {}

    /**
     * Send the mail to a recipient.
     *
     * @return void
     */
    final public function mail($recipient, $a=null) {
        $this->message->userto = $recipient;
        
        if ($a !== null) {
            $this->update_strings($a);
        }

        message_send($this-> message);
    }

    /**
     * Update strings on the message object.
     *
     * @param mixed $a The substitutions to make into the language string.
     *
     * @return void
     */
    final public function update_strings($a) {
        foreach (static::$strings as $string) {
            $property = "{$string}string";
            $this->message->{$string}
                    = util::real_string($this->{$property}, $a);
        }

        $this->message->fullmessage
                = format_text_email($this->message->fullmessagehtml, FORMAT_HTML);
    }
}
