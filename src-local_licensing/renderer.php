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

use local_licensing\capabilities;
use local_licensing\url_generator;
use local_licensing\util;

defined('MOODLE_INTERNAL') || die;

/**
 * Renderer.
 */
class local_licensing_renderer extends plugin_renderer_base {
    /**
     * Render a list of action buttons.
     *
     * @param \action_link[] $actionbuttons An array of action button components
     *                                      to render.
     *
     * @return string The rendered HTML.
     */
    protected function action_buttons($actionbuttons) {
        $renderedactionbuttons = array();
        foreach ($actionbuttons as $actionbutton) {
            $renderedactionbuttons[] = $this->render($actionbutton);
        }

        return html_writer::alist($renderedactionbuttons, array(
            'class' => 'action-buttons',
        ));
    }

    /**
     * Allocation counts.
     *
     * @param integer $count     The total number of licences allocated.
     * @param integer $consumed  The number of licences that have been reserved
     *                           by distributions.
     * @param integer $available The number of licences still available for
     *                           distribution.  
     *
     * @return string The generated HTML.
     */
    public function allocation_counts($count, $consumed, $available) {
        $items = array(
            'count'     => $count,
            'consumed'  => $consumed,
            'available' => $available,
        );

        $listitems = array();
        foreach ($items as $name => $value) {
            $listitems[] = util::string("allocation:{$name}x", $value);
        }

        return html_writer::alist($listitems);
    }

    /**
     * Allocation progress.
     *
     * @param integer $count    The total number of allocated licences.
     * @param integer $consumed The number of licences which have actually been
     *                          distributed.
     *
     * @return string The rendered HTML.
     */
    public function allocation_progress($count, $consumed) {
        return html_writer::tag('progress', '', array(
            'max'   => $count,
            'value' => $consumed,
        ));
    }

    /**
     * Allocation table.
     *
     * @param \local_licensing\model\allocation[] $allocations
     *
     * @return string The generated HTML.
     */
    public function allocation_table($allocations) {
        $head = array(
            util::string('allocation:targetset'),
            util::string('productset'),
            util::string('allocation:counts'),
            util::string('allocation:progress'),
            util::string('actions', null, 'moodle'),
        );

        $editurl   = url_generator::edit_allocation();
        $deleteurl = url_generator::delete_allocation();

        list($table, $editurl, $deleteurl)
                = $this->generic_table($head, $editurl, $deleteurl);

        foreach ($allocations as $allocation) {
            $editurl->url->param('id', $allocation->id);
            $deleteurl->url->param('id', $allocation->id);
            $actionbuttons = array($editurl, $deleteurl);

            $table->data[] = array(
                $this->edit_link('target_set', $allocation->targetsetid,
                                 $allocation->targetsetname),
                $this->edit_link('product_set', $allocation->productsetid,
                                 $allocation->productsetname),
                $this->allocation_counts($allocation->count,
                                         $allocation->consumed,
                                         $allocation->available),
                $this->allocation_progress($allocation->count,
                                           $allocation->consumed),
                $this->action_buttons($actionbuttons),
            );
        }

        return html_writer::table($table);
    }

    /**
     * Back to all items link.
     *
     * @param string $tab           The name of the tab.
     * @param string $tablangstring The name of the tab's language string key.
     *
     * @return string The generated HTML.
     */
    public function back_to_all($tab, $tablangstring) {
        return html_writer::link(url_generator::list_url($tab),
                                 util::string("{$tablangstring}:backtoall"),
                                 array('class' => 'back-to-all'));
    }

    /**
     * Distribution table.
     *
     * @param \local_licensing\model\distribution[] $distributions
     *
     * @return string The generated HTML.
     */
    public function distribution_table($distributions) {
        $head = array(
            util::string('distribution:productset'),
            util::string('distribution:product'),
            util::string('distribution:count'),
            util::string('actions', null, 'moodle'),
        );

        $editurl   = url_generator::edit_distribution();
        $deleteurl = url_generator::delete_distribution();

        list($table, $editurl, $deleteurl)
                = $this->generic_table($head, $editurl, $deleteurl);

        foreach ($distributions as $distribution) {
            $editurl->url->param('id', $distribution->id);
            $deleteurl->url->param('id', $distribution->id);
            $actionbuttons = array($editurl, $deleteurl);

            $productset = $distribution->get_allocation()->get_product_set();
            $product    = $distribution->get_product();

            $table->data[] = array(
                $this->edit_link('product_set', $productset->id,
                                 $productset->name),
                html_writer::link($product->get_url(), $product->get_name()),
                $distribution->get_count(),
                $this->action_buttons($actionbuttons),
            );
        }

        return html_writer::table($table);
    }

    /**
     * Item edit link.
     *
     * @param string  $tab
     * @param integer $id
     * @param string  $name
     *
     * @return string The rendered HTML.
     */
    protected function edit_link($tab, $id, $name) {
        return html_writer::link(url_generator::edit_url($tab, $id), $name);
    }

    /**
     * Do the ground work for rendering a table.
     *
     * @param mixed[] $head      An array of html_table_cell objects or strings.
     * @param string  $editurl   The URL to link edit buttons to.
     * @param string  $deleteurl The URL to link delete buttons to.
     *
     * @return mixed[] A numerically-indexed array containing three values:
     *                 [0] => html_table  $table
     *                 [1] => action_link $deletelink
     *                 [2] => action_link $editlink
     */
    protected function generic_table($head, $editurl, $deleteurl) {
        $table = new html_table();
        $table->head = $head;

        $deleteicon = new pix_icon('t/delete', new lang_string('delete'));
        $deletelink = new action_link($deleteurl, $deleteicon, null,
                                      array('title' => new lang_string('delete')));
        $editicon   = new pix_icon('t/edit', new lang_string('edit'));
        $editlink   = new action_link($editurl, $editicon, null,
                                      array('title' => new lang_string('edit')));

        return array($table, $deletelink, $editlink);
    }

    /**
     * List of objects within a set.
     *
     * @param \local_licensing\model\base_model[] $items
     *
     * @return string The generated HTML.
     */
    public function set_list($items) {
        $listitems = array();
        foreach ($items as &$item) {
            $listitems[] = html_writer::link($item->get_url(),
                                             $item->get_name());
        }

        return html_writer::alist($listitems);
    }

    /**
     * Product set table.
     *
     * @param \local_licensing\model\product_set[] $productsets
     *
     * @return string The generated HTML.
     */
    public function product_set_table($productsets) {
        $head = array(
            util::string('productset'),
            util::string('products'),
            util::string('actions', null, 'moodle'),
        );

        $editurl   = url_generator::edit_product_set();
        $deleteurl = url_generator::delete_product_set();

        list($table, $editurl, $deleteurl)
                = $this->generic_table($head, $editurl, $deleteurl);

        foreach ($productsets as $productset) {
            $editurl->url->param('id', $productset->id);
            $deleteurl->url->param('id', $productset->id);
            $actionbuttons = array($editurl, $deleteurl);

            $table->data[] = array(
                $productset->name,
                $this->set_list($productset->get_products()),
                $this->action_buttons($actionbuttons),
            );
        }

        return html_writer::table($table);
    }

    /**
     * Target table.
     *
     * @param \local_licensing\model\target[] $targets
     *
     * @return string The generated HTML.
     */
    public function target_set_table($targetsets) {
        $head = array(
            util::string('targetset'),
            util::string('targetset:targets'),
            util::string('actions', null, 'moodle'),
        );

        $editurl   = url_generator::edit_target_set();
        $deleteurl = url_generator::delete_target_set();

        list($table, $editurl, $deleteurl)
                = $this->generic_table($head, $editurl, $deleteurl);

        foreach ($targetsets as $targetset) {
            $editurl->url->param('id', $targetset->id);
            $deleteurl->url->param('id', $targetset->id);
            $actionbuttons = array($editurl, $deleteurl);

            $table->data[] = array(
                $targetset->name,
                $this->set_list($targetset->get_targets()),
                $this->action_buttons($actionbuttons),
            );
        }

        return html_writer::table($table);
    }

    /**
     * Administration tabs.
     *
     * @param string $selected An (optional) selected tab's name.
     *
     * @return string The generated HTML.
     */
    public function tabs($selected=null) {
        $context = context_system::instance();
        $taburls = url_generator::tabs();

        $tabs = array();
        foreach ($taburls as $tabname => $taburl) {
            if (capabilities::has_for_tab($tabname, $context)) {
                $tabstring = str_replace('_', '', $tabname);
                $tabs[] = new tabobject($tabname, $taburl,
                                        util::string($tabstring));
            }
        }

        return $this->tabtree($tabs, $selected);
    }
}
