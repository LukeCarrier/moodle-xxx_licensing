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

Y.namespace('Moodle.local_licensing.productdialogue');
M.local_licensing = M.local_licensing || {};

/**
 * Product dialogue.
 *
 * Prompts the user to select a bunch of products of a specified type, then
 * inserts the comma-separated IDs of the selected items into a form field.
 *
 * @see ProductDialogue.initializer
 */
var ProductDialogue = function(config) {
    config.center    = true;
    config.draggable = true;
    config.modal     = true;
    config.visible   = false;

    ProductDialogue.superclass.constructor.apply(this, [config]);
};
ProductDialogue.NAME = NAME;
ProductDialogue.ATTRS = {
    /**
     * URL to make AJAX queries to.
     *
     * @var string
     */
    ajaxurl: {},

    /**
     * Product type.
     *
     * @var string
     */
    type: {}
};

Y.extend(ProductDialogue, M.core.dialogue, {
    /**
     * Initialiser.
     *
     * @param mixed[] config Configuration options.
     *
     * @return void
     */
    initializer: function(config) {
        var body = Y.Node.create('<p>Body text goes here</p>'),
            head = Y.Node.create('<h1>' + this.selectString() + '</h1>');

        this.setDialogueContent(body, head);
        this.setupForm();
        this.setupEvents();
    },

    /**
     * Handle a change to the search field.
     */
    handleQueryChange: function(e) {
        console.log(query);

        Y.io(M.cfg.wwwroot + this.get('ajaxurl'), {
            context: this,
            on: {
                complete: this.updateProductsList
            }
        });
    },

    selectString: function() {
        return this.string('selectxs', this.typeString());
    },

    /**
     * Set the dialogue's content.
     *
     * @param DOMNode body
     * @param DOMNode head
     *
     * @return void
     */
    setDialogueContent: function(body, head) {
        this.setStdModContent(Y.WidgetStdMod.BODY, body,
                              Y.WidgetStdMod.REPLACE);
        this.setStdModContent(Y.WidgetStdMod.HEADER, head,
                              Y.WidgetStdMod.REPLACE);
    },

    /**
     * Initialise events.
     *
     * @return void
     */
    setupEvents: function() {

    },

    /**
     * Initialise the form.
     *
     * Removes the text input fields we use for transmitting product ID numbers
     * to the submission, then replaces them with our dialog launching buttons.
     *
     * @return void
     */
    setupForm: function() {
        var type      = this.get('type'),
            container = Y.one('#fitem_id_products' + type),
            textInput = container.one('#id_products' + type),
            button    = Y.Node.create('<button id="licensing_' + type + '">'
                                      + this.selectString() + '</button>');

        container.append(button);
        textInput.hide();
        button.on('click', function(e) {
            this.show();

            e.preventDefault();
            e.stopPropagation();
        }, this);
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
     * @return string The friendly name of our product type.
     */
    typeString: function() {
        return this.string('productset:products:' + this.get('type'));
    },

    updateProductsList: function(tid, response, args) {
        console.log(tid, response, args);

        try {
            var products = Y.JSON.parse(outcome.responseText);
            if (products.error) {
                new M.core.ajaxException(products);
            } else {
                this.setCohorts(products.response);
            }
        } catch (e) {
            return new M.core.exception(e);
        }
    }
});

Y.Moodle.local_licensing.productdialogue = ProductDialogue;


/**
 * Moodle wrapper around the product dialogue.
 *
 * @param mixed[] config
 *
 * @see ProductDialogue
 */
M.local_licensing.init_product_dialogue = function(config) {
    return new ProductDialogue(config);
};
