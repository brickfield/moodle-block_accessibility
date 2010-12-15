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
 * Define the accessibility block's class
 *
 * @package    blocks
 * @subpackage  accessibility
 * @author      Mark Johnson <mark.johnson@tauntons.ac.uk>
 * @copyright   2010 Tauntons College, UK
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * accessibility Block's class
 */
class block_accessibility extends block_base {

    /**
     * Set the title
     */
    function init() {
        $this->title = get_string('pluginname', 'block_accessibility');
    }

    /**
     * Set where the block should be allowed to be added
     *
     * @return array
     */
    function applicable_formats() {
        return array('all' => true);
    }

    /**
     * Generate the contents for the block
     *
     * @return object Block contents and footer
     */
    function get_content () {
        if ($this->content !== null) {
            return $this->content;
        }

        $this->content->footer = '';
        $this->content->text = html_writer::tag('span', get_string('requiresjavascript', 'block_accessibility'), array('id' => 'block_accessibility_placeholder'));

        $jsmodule = array(
            'name'  =>  'block_accessibility',
            'fullpath'  =>  '/blocks/accessibility/module.js',
            'requires'  =>  array('base', 'node')
        );

        $this->page->requires->string_for_js('launchtoolbar', 'block_accessibility');
        $this->page->requires->js_init_call('M.block_accessibility.init', null, false, $jsmodule);

        return $this->content;
    }

}
?>