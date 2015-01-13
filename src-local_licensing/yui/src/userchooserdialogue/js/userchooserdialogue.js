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

Y.namespace('Moodle.local_licensing');
M.local_licensing = M.local_licensing || {};

/**
 * User dialogue.
 *
 * Prompts the user to select a bunch of users of a specified type, then
 * inserts the comma-separated IDs of the selected items into a form field. The
 * ability to search users is provided via a simple text field.
 *
 * @see UserChooserDialogue.initializer
 */
UserChooserDialogue = function(config) {
    UserChooserDialogue.superclass.constructor.apply(this, [config]);
};

UserChooserDialogue.NAME = NAME;

Y.extend(UserChooserDialogue, Y.Moodle.local_licensing.ChooserDialogue, {
    /**
     * Add the created user to the list of selected objects.
     *
     * @param object user The created user.
     */
    addCreatedUser: function(user) {
        this.addSelectedObjectId(user.id);
    },

    /**
     * Create a user.
     *
     * @param object   user
     * @param callable onComplete
     *
     * @return void
     */
    createUser: function(user, onComplete) {
        var params = Y.merge(user, {
            action:     'create',
            objecttype: 'user'
        });

        this.io(params, onComplete);
    },

    /**
     * Handle a click on the create user button.
     *
     * @param DOMEventFacade e
     */
    handleCreate: function(e) {
        var form       = e.target.get('parentNode').one('.form'),
            user       = {},
            properties = ['firstname', 'lastname', 'username', 'password',
                          'email', 'idnumber'];

        Y.Array.each(properties, function(property) {
            user[property]
                    = form.one('input[name="' + property + '"]').get('value');
        });

        this.createUser(user, this.addCreatedUser);
    },

    /**
     * Handle a click on the create button in the footer.
     *
     * @return void
     */
    handleSwitch: function(e) {
        var to = e.target.getData('to');

        if (to === 'create') {
            var params = {
                isCreating: true
            };

            this.setStdModContent(Y.WidgetStdMod.BODY,
                                  Y.Moodle.local_licensing.userchooserdialogue.formtemplate({}),
                                  Y.WidgetStdMod.REPLACE);
            this.setStdModContent(Y.WidgetStdMod.FOOTER,
                                  Y.Moodle.local_licensing.userchooserdialogue.footertemplate(params),
                                  Y.WidgetStdMod.REPLACE);
        } else {
            this.updateDialogueBody([]);
        }
    },

    /**
     * @override Y.Moodle.local_licensing.ChooserDialogue
     */
    initialiseEvents: function(objects) {
        var root = this.get('srcNode');

        this.constructor.superclass.initialiseEvents.apply(this, arguments);

        root.delegate('click', this.handleCreate, '.create',    this);
        root.delegate('click', this.handleSwitch, '.switch-ui', this);
    },

    /**
     * @override Y.Moodle.local_licensing.ChooserDialogue
     */
    typeString: function() {
        return this.string('distribution:users');
    },

    /**
     * @override Y.Moodle.local_licensing.ChooserDialogue
     */
    updateDialogueBody: function(objects) {
        var params = {
            isCreating: false
        };

        this.constructor.superclass.updateDialogueBody.apply(this, arguments);

        this.setStdModContent(Y.WidgetStdMod.FOOTER,
                              Y.Moodle.local_licensing.userchooserdialogue.footertemplate(params),
                              Y.WidgetStdMod.REPLACE);
    }
});

Y.Moodle.local_licensing.UserChooserDialogue = UserChooserDialogue;

/**
 * Moodle wrapper around the user dialogue.
 *
 * @param mixed[] config
 *
 * @see UserChooserDialogue
 */
M.local_licensing.init_user_dialogue = function(config) {
    config.objecttype = 'user';

    return new UserChooserDialogue(config);
};
