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
 * Target dialogue.
 *
 * Prompts the user to select a bunch of Targets of a specified type, then
 * inserts the comma-separated IDs of the selected items into a form field. The
 * ability to search Targets is provided via a simple text field.
 *
 * @see UpdateSelectOptions.initializer
 */
UpdateSelectOptions = function(config) {
    UpdateSelectOptions.superclass.constructor.apply(this, [config]);
};

UpdateSelectOptions.NAME = NAME;
UpdateSelectOptions.ATTRS = {
    ajaxurl: {},
    param: {},
    params: {},
    source: {},
    target: {}
};

Y.extend(UpdateSelectOptions, Y.Base, {
    /**
     * Initialiser.
     *
     * @param mixed[] config Configuration options.
     *
     * @return void
     */
    initializer: function(config) {
        var sourceNode = Y.one(this.get('source'));

        this.getOptions(sourceNode.get('value'));

        sourceNode.on('change', this.handleSourceChange, this);
    },

    /**
     * Add an option to the target field.
     *
     * @param object item An object with fullname and id properties.
     *
     * @return void
     */
    addOption: function(item) {
        var option = this.makeOption(item),
            target = Y.one(this.get('target'));

        target.append(option);
    },

    /**
     * Add an optgroup to the target field.
     *
     * @param string   name
     * @param object[] options
     *
     * @return void
     */
    addOptionGroup: function(name, options) {
        var optGroup = Y.Node.create('<optgroup label="' + name + '"></optgroup>'),
            target   = Y.one(this.get('target'));

        Y.Array.each(options, function(item) {
            optGroup.append(this.makeOption(item));
        }, this);

        target.append(optGroup);
    },

    /**
     * Get product options for a given product set ID.
     *
     * @param integer value
     *
     * @return void
     */
    getOptions: function(value) {
        var param  = this.get('param'),
            params = this.get('params');

        params[param] = value;

        Y.io(M.cfg.wwwroot + this.get('ajaxurl'), {
            method: 'GET',
            data: build_querystring(params),

            context: this,
            on: {
                complete: this.handleQueryComplete
            }
        });
    },

    /**
     * Handle IO operation complete.
     *
     * @param tid
     * @param response
     * @param args
     *
     * @return void
     */
    handleQueryComplete: function(tid, response, args) {
        var targetNode = this.get('target');

        try {
            var response = Y.JSON.parse(response.responseText);

            if (response.error) {
                new M.core.ajaxException(response);
            } else {
                this.updateTargetOptions(response.response);
            }
        } catch (e) {
            return new M.core.exception(e);
        }
    },

    /**
     * Handle a change event on the source select.
     *
     * @param DOMEventFacade e
     *
     * @return void
     */
    handleSourceChange: function(e) {
        this.getOptions(e.target.get('value'));
    },

    /**
     * Make an option.
     *
     * @param object item An object with fullname and id properties.
     *
     * @return Y_Node
     */
    makeOption: function(item) {
        return Y.Node.create('<option value="' + item.id + '">' + item.fullname + '</option>');
    },

    /**
     * Update target select options.
     *
     * @return void
     */
    updateTargetOptions: function(options) {
        var target = Y.one(this.get('target'));

        target.get('childNodes').remove();

        Y.Object.each(options, function(item, index) {
            if (Y.Lang.isArray(item)) {
                this.addOptionGroup(index, item);
            } else {
                this.addOption(item);
            }
        }, this);
    }
});

Y.Moodle.local_licensing.UpdateSelectOptions = UpdateSelectOptions;

/**
 * Moodle wrapper around the select option updater.
 *
 * @param mixed[] config
 *
 * @see UpdateSelectOptions
 */
M.local_licensing.init_update_select_options = function(config) {
    return new UpdateSelectOptions(config);
};
