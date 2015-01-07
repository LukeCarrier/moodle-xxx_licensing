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
 * Product dialogue.
 *
 * Prompts the user to select a bunch of products of a specified type, then
 * inserts the comma-separated IDs of the selected items into a form field. The
 * ability to search products is provided via a simple text field.
 *
 * @see ProductChooserDialogue.initializer
 */
ProductChooserDialogue = function(config) {
    ProductChooserDialogue.superclass.constructor.apply(this, [config]);
};

ProductChooserDialogue.NAME = NAME;

Y.extend(ProductChooserDialogue, Y.Moodle.local_licensing.ChooserDialogue, {
    /**
     * @override Y.Moodle.local_licensing.ChooserDialogue
     */
    typeString: function() {
        return this.string('productset:products:' + this.get('type'));
    }
});

Y.Moodle.local_licensing.ProductChooserDialogue = ProductChooserDialogue;

/**
 * Moodle wrapper around the product dialogue.
 *
 * @param mixed[] config
 *
 * @see ProductChooserDialogue
 */
M.local_licensing.init_product_dialogue = function(config) {
    config.objecttype = 'product';

    return new ProductChooserDialogue(config);
};
