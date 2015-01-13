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

/**
 * Base chooser dialogue.
 *
 * An extension of the Moodle dialogue module that provides end users with an
 * AJAX-backed search form for selecting objects. The IDs of the selected
 * objects are concatenated together to form a comma-separated string and then
 * inserted into a hidden form field for server-side processing.
 *
 * @see ChooserDialogue.initializer
 */
var ChooserDialogue = function(config) {
    config.center    = true;
    config.draggable = true;
    config.modal     = true;
    config.visible   = false;

    ChooserDialogue.superclass.constructor.apply(this, [config]);
};

ChooserDialogue.NAME = NAME;
ChooserDialogue.ATTRS = {
    /**
     * URL to make AJAX queries to.
     *
     * @var string
     */
    ajaxurl: {},

    /**
     * Object type.
     */
    objecttype: {},

    /**
     * Object subtype.
     *
     * @var string
     */
    type: {}
};

Y.extend(ChooserDialogue, M.core.dialogue, {
    /**
     * Initialiser.
     *
     * @param mixed[] config Configuration options.
     *
     * @return void
     */
    initializer: function(config) {
        this.initialiseDialogueContents();
        this.initialiseForm();
        this.initialiseEvents();

        this.refreshObjectList();
    },

    /**
     * Get the 'add {object type}s' string.
     *
     * @return string
     */
    addString: function() {
        return this.string('addxs', this.typeString());
    },

    /**
     * Get a list of objects matching the specified query.
     *
     * @param string field Either "ids" or "term".
     * @param string query Either an array of ID numbers or a search term as a
     *                     string.
     * @param callable onComplete The callback to call when complete.
     */
    getObjectList: function(field, query, onComplete) {
        var params = {
            objecttype: this.get('objecttype')
        };

        if (this.hasSubtypes()) {
            params['type'] = this.get('type');
        }

        params[field] = query;

        Y.io(M.cfg.wwwroot + this.get('ajaxurl'), {
            method: 'GET',
            data: build_querystring(params),

            context: this,
            on: {
                complete: this.handleQueryComplete(onComplete)
            }
        });
    },

    /**
     * Get the selected object IDs.
     *
     * @return integer[]
     */
    getSelectedObjectIds: function() {
        var selectedIdInput = this.getSelectedObjectIdInput(),
            selectedIds     = selectedIdInput.get('value');

        return (selectedIds === '') ? []
                                    : Y.Array.dedupe(selectedIds.split(','));
    },

    /**
     * Get form field ID.
     *
     * Moodle's moodleform library generates a bunch of element IDs for inputs,
     * containers and labels alike based upon the name given for the field.
     *
     * @return string The name of the field.
     */
    getFormFieldId: function() {
        var formFieldId = 'id_' + this.get('objecttype');

        if (this.hasSubtypes()) {
            formFieldId += 's' + this.get('type');
        }

        return formFieldId;
    },

    /**
     * Get the selected object ID input.
     *
     * @return Y_Node
     */
    getSelectedObjectIdInput: function() {
        return Y.one('#' + this.getFormFieldId());
    },

    /**
     * Get the selected object list.
     *
     * @return Y_Node
     */
    getSelectedObjectList: function() {
        return Y.one('#fitem_' + this.getFormFieldId() + ' .list');
    },

    /**
     * Get a comma-separated list of all of the selected objects' IDs.
     *
     * This is necessary since Moodle doesn't allow us to access arrays of GET
     * parameters in ajax.php.
     *
     * @return string
     */
    getSelectedObjectIdString: function() {
        return this.getSelectedObjectIds().join(',');
    },

    /**
     * Handle the addition of an object to the selected object list.
     *
     * @param DOMEventFacade e
     *
     * @return void
     */
    handleAdd: function(e) {
        var objectId          = e.target.getData('objectId'),
            selectedObjectIds = this.getSelectedObjectIds();

        selectedObjectIds.push(objectId);
        this.setSelectedObjectIds(selectedObjectIds);

        this.refreshObjectList();
    },

    /**
     * Wrap a callback in the YUI JSON parse dance.
     *
     * @param callable onComplete The function to call with the JSON-decoded
     *                            response body.
     *
     * @return void
     */
    handleQueryComplete: function(onComplete) {
        var onComplete = Y.bind(onComplete, this);

        return function(tid, response, args) {
            try {
                var response = Y.JSON.parse(response.responseText);

                if (response.error) {
                    new M.core.ajaxException(response);
                } else {
                    onComplete(response.response);
                }
            } catch (e) {
                return new M.core.exception(e);
            }
        }
    },

    /**
     * Handle a click on the search button.
     *
     * We need get the current value of the search field, query the server for
     * matching objects and then update the dialogue body with the new result
     * set.
     *
     * @param DOMEventFacade e
     *
     * @return void
     */
    handleSearch: function(e) {
        var submitNode = e.currentTarget,
            termNode   = submitNode.previous('input[name="searchterm"]'),
            term       = termNode.get('value');

        this.getObjectList('term', term, this.updateDialogueBody);
    },

    /**
     * Set the dialogue's content.
     *
     * @param DOMNode head
     *
     * @return void
     */
    initialiseDialogueContents: function() {
        this.updateDialogueBody([]);
        this.setStdModContent(Y.WidgetStdMod.HEADER,
                              Y.Node.create('<h1>' + this.addString() + '</h1>'),
                              Y.WidgetStdMod.REPLACE);
    },

    /**
     * Initialise events.
     *
     * @return void
     */
    initialiseEvents: function() {
        var root = this.get('srcNode');

        root.delegate('click', this.handleSearch, '.search', this);
        root.delegate('click', this.handleAdd,    '.add',    this);
    },

    /**
     * Initialise the form.
     *
     * Removes the text input fields we use for transmitting object ID numbers
     * to the submission, then replaces them with our dialog launching buttons.
     *
     * @return void
     */
    initialiseForm: function() {
        var type      = this.get('type'),
            container = Y.one('#fitem_' + this.getFormFieldId() + ' .felement'),
            textInput = this.getSelectedObjectIdInput();

        var formElements = Y.Moodle.local_licensing.chooserdialogue.formtemplate({
            add:  this.addString()
        });

        textInput.hide();

        container.append(formElements);
        container.one('.add').on('click', function(e) {
            this.show();

            e.preventDefault();
            e.stopPropagation();
        }, this);
    },

    /**
     * Does the object type have subtypes?
     *
     * @return boolean True if yes, false if no.
     */
    hasSubtypes: function() {
        return !!this.get('type');
    },

    /**
     * Refresh the list of selected objects displayed in the form.
     *
     * @return void
     */
    refreshObjectList: function() {
        var ids = this.getSelectedObjectIdString();

        if (ids !== '') {
            this.getObjectList('ids', ids, this.updateListBody);
        }
    },

    /**
     * Set the selected object IDs.
     *
     * @param integer[] selectedIds
     *
     * @return void
     */
    setSelectedObjectIds: function(selectedIds) {
        var selectedIdInput = this.getSelectedObjectIdInput();

        selectedIdInput.set('value', Y.Array.dedupe(selectedIds));
    },

    /**
     * Shorthand get_string() wrapper.
     *
     * @param string identifier
     * @param mixed  a
     * @param string component
     *
     * @return string
     */
    string: function(identifier, a, component) {
        if (typeof component === 'undefined') {
            component = 'local_licensing';
        }

        return M.util.get_string(identifier, component, a);
    },

    /**
     * Get the friendly name of this type.
     *
     * @return string The friendly name of our object type.
     */
    typeString: function() {
        return new M.core.exception();
    },

    /**
     * Update the dialogue's body with a new object list.
     *
     * A list of objects matching the user's search term was successfully
     * retrieved, so we now need to update the dialogue's contents to reflect
     * the user's query.
     *
     * @param mixed[][] objects
     *
     * @return void
     */
    updateDialogueBody: function(objects) {
        var params = {
            objects: objects,
            searchTerm: '',
            type: this.typeString()
        };

        this.setStdModContent(Y.WidgetStdMod.BODY,
                              Y.Moodle.local_licensing.chooserdialogue.objectlisttemplate(params),
                              Y.WidgetStdMod.REPLACE);
    },

    /**
     * Update the selected object list.
     *
     * @param object[] selectedObjects The array of the selected objects.
     *
     * @return void
     */
    updateListBody: function(selectedObjects) {
        var params = {
            selectedObjects: selectedObjects
        };

        var objectList      = this.getSelectedObjectList(),
            objectListItems = Y.Moodle.local_licensing.chooserdialogue.selectedobjectlisttemplate(params);

        objectList.setHTML(objectListItems);
    },
});

Y.Moodle.local_licensing.ChooserDialogue = ChooserDialogue;
