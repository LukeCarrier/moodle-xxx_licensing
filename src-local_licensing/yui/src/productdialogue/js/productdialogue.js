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
 * inserts the comma-separated IDs of the selected items into a form field. The
 * ability to search products is provided via a simple text field.
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
        this.initialiseDialogueContents();
        this.setupForm();
        this.setupEvents();
    },

    /**
     * Get the 'add {product type}s' string.
     *
     * @return string
     */
    addString: function() {
        return this.string('addxs', this.typeString());
    },

    /**
     * Get the selected product IDs.
     *
     * @return integer[]
     */
    getSelectedProductIds: function() {
        var selectedIdInput = this.getSelectedProductIdInput(),
            selectedIds     = selectedIdInput.get('value');

        return (selectedIds === '') ? []
                                    : Y.Array.dedupe(selectedIds.split(','));
    },

    /**
     * Get a comma-separated list of all of the selected products' IDs.
     *
     * This is necessary since Moodle doesn't allow us to access arrays of GET
     * parameters in ajax.php.
     *
     * @return string
     */
    getSelectedProductIdString: function() {
        return this.getSelectedProductIds().join(',');
    },

    /**
     * Set the selected product IDs.
     *
     * @param integer[] selectedIds
     *
     * @return void
     */
    setSelectedProductIds: function(selectedIds) {
        var selectedIdInput = this.getSelectedProductIdInput();

        selectedIdInput.set('value', Y.Array.dedupe(selectedIds));
    },

    /**
     * Get the selected product ID input.
     *
     * @return Y_Node
     */
    getSelectedProductIdInput: function() {
        return Y.one('#id_products' + this.get('type'));
    },

    /**
     * Get the selected product list.
     *
     * @return Y_Node
     */
    getSelectedProductList: function() {
        return Y.one('#licensing_' + this.get('type') + '_list');
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
    setupEvents: function() {
        var root = this.get('srcNode');

        root.delegate('click', this.handleSearch, '.search', this);
        root.delegate('click', this.handleAdd,    '.add',    this);
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
        var type         = this.get('type'),
            container    = Y.one('#fitem_id_products' + type + ' .felement'),
            textInput    = container.one('#id_products' + type);

        var formElements = Y.Moodle.local_licensing.formtemplate({
            add:  this.addString(),
            type: type
        });

        textInput.hide();

        container.append(formElements);
        Y.one('#licensing_' + type + '_add').on('click', function(e) {
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

    /**
     * Handle the addition of a product to
     *
     * @param DOMEventFacade e
     *
     * @return void
     */
    handleAdd: function(e) {
        var productId          = e.target.getData('productId'),
            selectedProductIds = this.getSelectedProductIds();

        selectedProductIds.push(productId);
        this.setSelectedProductIds(selectedProductIds);

        this.getProductList('ids', this.getSelectedProductIdString(),
                            this.updateListBody);
    },

    /**
     * Handle a click on the search button.
     *
     * We need get the current value of the search field, query the server for
     * matching products and then update the dialogue body with the new result
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

        this.getProductList('term', term, this.updateDialogueBody);
    },

    /**
     * Get a list of products matching the specified query.
     *
     * @param string field Either "ids" or "term".
     * @param string query Either an array of ID numbers or a search term as a
     *                     string.
     * @param callable onComplete The callback to call when complete.
     */
    getProductList: function(field, query, onComplete) {
        var params = {
            type: 'product',
            producttype: this.get('type')
        };

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
     * Wrap a callback in the YUI JSON parse dance.
     *
     * @param callable onComplete The function to call with the JSON-decoded
     *                            response body.
     *
     * @return void
     */
    handleQueryComplete: function(onComplete) {
        onComplete = Y.bind(onComplete, this);

        return function(tid, response, args) {
            try {
                response = Y.JSON.parse(response.responseText);

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
     * Update the dialogue's body with a new product list.
     *
     * A list of products matching the user's search term was successfully
     * retrieved, so we now need to update the dialogue's contents to reflect
     * the user's query.
     *
     * @param mixed[][] products
     *
     * @return void
     */
    updateDialogueBody: function(products) {
        var params = {
            products: products,
            productType: this.typeString(),
            searchTerm: ''
        };

        this.setStdModContent(Y.WidgetStdMod.BODY,
                              Y.Moodle.local_licensing.productlisttemplate(params),
                              Y.WidgetStdMod.REPLACE);
    },

    updateListBody: function(selectedProducts) {
        var params = {
            selectedProducts: selectedProducts
        };

        var productList      = this.getSelectedProductList(),
            productListItems = Y.Moodle.local_licensing.selectedproductlisttemplate(params);

        productList.setHTML(productListItems);
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
