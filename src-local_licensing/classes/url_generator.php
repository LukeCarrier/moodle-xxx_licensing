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

namespace local_licensing;

use moodle_url;

/**
 * URL generator.
 */
class url_generator {
    /**
     * Base URL for pages located within our plugin.
     *
     * @var string
     */
    const BASE_URL = '/local/licensing';

    /**
     * Get the URL to add an allocation.
     *
     * @return \moodle_url The URL.
     */
    public static function add_allocation() {
        return static::edit_allocation();
    }

    /**
     * Get the URL to add a distribution.
     *
     * @return \moodle_url The URL.
     */
    public static function add_distribution() {
        return static::edit_distribution();
    }

    /**
     * Get the URL to add a product set.
     *
     * @return \moodle_url The URL.
     */
    public static function add_product_set() {
        return static::edit_product_set();
    }

    public static function add_target_set() {
        return static::edit_target_set();
    }

    /**
     * Get the AJAX base URL.
     *
     * @return \moodle_url The URL.
     */
    public static function ajax() {
        return static::local_url('/ajax.php');
    }

    /**
     * Get the URL to delete an allocation.
     *
     * @param integer $id The (optional) allocation ID.
     *
     * @return \moodle_url The URL.
     */
    public static function delete_allocation($id=null) {
        return static::delete_url('allocation', $id);
    }

    /**
     * Get the URL to delete a distribution.
     *
     * @param integer $id The (optional) distribution ID.
     *
     * @return \moodle_url The URL.
     */
    public static function delete_distribution($id=null) {
        return static::delete_url('distribution', $id);
    }

    /**
     * Get the URL to delete a product set.
     *
     * @param integer $id The (optional) product set ID.
     *
     * @return \moodle_url The URL.
     */
    public static function delete_product_set($id=null) {
        return static::delete_url('product_set', $id);
    }

    public static function delete_target_set($id=null) {
        return static::delete_url('target_set', $id);
    }

    /**
     * Get the URL to delete an item of a specific type.
     *
     * @param string  The name of the tab.
     * @param integer The (optional) ID of the item to delete.
     *
     * @return \moodle_url The URL.
     */
    public static function delete_url($tab, $id=null) {
        $url = static::local_url('/delete.php', array(
            'tab' => $tab,
        ));

        if ($id !== null) {
            $url->param('id', $id);
        }

        return $url;
    }

    public static function edit_allocation($id=null) {
        return static::edit_url('allocation', $id);
    }

    public static function edit_distribution($id=null) {
        return static::edit_url('distribution', $id);
    }

    /**
     * Get the URL to edit a product set.
     *
     * @param integer $id The (optional) product set ID.
     *
     * @return \moodle_url The URL.
     */
    public static function edit_product_set($id=null) {
        return static::edit_url('product_set', $id);
    }

    public static function edit_target_set($id=null) {
        return static::edit_url('target_set', $id);
    }

    public static function edit_url($tab, $id) {
        $url = static::local_url('/edit.php', array(
            'tab' => $tab,
        ));

        if ($id !== null) {
            $url->param('id', $id);
        }

        return $url;
    }

    /**
     * Get the URL of the index page.
     *
     * @return \moodle_url The URL.
     */
    public static function index() {
        return static::local_url('/index.php');
    }

    public static function list_allocations() {
        return static::list_url('allocation');
    }

    public static function list_distributions() {
        return static::list_url('distribution');
    }

    public static function list_product_sets() {
        return static::list_url('product_set');
    }

    public static function list_target_sets() {
        return static::list_url('target_set');
    }

    /**
     * Get the URL to list all items of a specific type.
     *
     * @param string The name of the tab.
     *
     * @return \moodle_url The URL.
     */
    public static function list_url($tab) {
        $url = static::index();
        $url->param('tab', $tab);

        return $url;
    }

    /**
     * Shorthand helper for local URLs.
     *
     * @param string  $url    The URL, within the confines of BASE_URL.
     * @param mixed[] $params An (optional) array of GET parameters to append to
     *                        the URL.
     *
     * @return \moodle_url The URL.
     */
    protected static function local_url($url, $params=null) {
        if ($params === null) {
            $params = array();
        }

        return new moodle_url(static::BASE_URL . $url, $params);
    }

    /**
     * URLs of all tabs.
     *
     * @return \moodle_url[] An array of Moodle URLs, indexed by their
     *                       corresponding tab names.
     */
    public static function tabs() {
        return array(
            'overview'     => static::index(),
            'target_set'   => static::list_target_sets(),
            'product_set'  => static::list_product_sets(),
            'allocation'   => static::list_allocations(),
            'distribution' => static::list_distributions(),
        );
    }
}
