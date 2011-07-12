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

require_once($CFG->dirroot.'/blocks/accessibility/lib.php');

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
        global $CFG;
        global $USER;
        global $FULLME;
        global $DB;
        if ($this->content !== null) {
            return $this->content;
        }

        $cssurl = '/blocks/accessibility/userstyles.php';
        $this->page->requires->css($cssurl);

        $size_url = new moodle_url('/blocks/accessibility/changesize.php', array('redirect' => $FULLME));
        $colour_url = new moodle_url('/blocks/accessibility/changecolour.php', array('redirect' => $FULLME));
        $db_url = new moodle_url('/blocks/accessibility/database.php', array('op' => 'save', 'size' => true, 'scheme' => true, 'redirect' => $FULLME));

        $inc_attrs = array('title' => get_string('inctext', 'block_accessibility'), 'id' => "block_accessibility_inc", 'class' => 'outer right', 'href' => $size_url->out(false, array('op' => 'inc')));
        $dec_attrs = array('title' => get_string('dectext', 'block_accessibility'), 'id' => "block_accessibility_dec", 'class' => 'outer', 'href' => $size_url->out(false, array('op' => 'dec')));
        $save_attrs = array('title' => get_string('save', 'block_accessibility'), 'id' => "block_accessibility_save");
        if (isset($USER->fontsize)) {
            if (accessibility_getsize($USER->fontsize) == 10) {
                $dec_attrs['class'] = 'outer disabled';
                unset($dec_attrs['href']);
            }
            if (accessibility_getsize($USER->fontsize) == 26) {
                $inc_attrs['class'] = 'outer disabled';
                unset($inc_attrs['href']);
            }
        }
        if (isset($USER->username) && (isset($USER->fontsize) || isset($USER->colourscheme))) {
            $save_attrs['class'] = 'outer right';
            $save_attrs['href'] = $db_url->out(false);
            $saveicon_url = new moodle_url('/blocks/accessibility/pix/document-save.png');
        } else {
            $save_attrs['class'] = 'outer disabled right';
            $saveicon_url = new moodle_url('/blocks/accessibility/pix/document-save-grey.png');
        }

        $content = '';

      $content .= html_writer::start_tag('div', array('id' => 'accessibility_controls', 'class' => 'content'));
        $content .= html_writer::start_tag('ul', array('id' => 'block_accessibility_textresize', 'class' => 'button_row'));

            $content .= html_writer::start_tag('li', array('class' => 'access-button'));
                $content .= html_writer::tag('a', get_string('char', 'block_accessibility').'-', $dec_attrs);
            $content .= html_writer::end_tag('li');

            $content .= html_writer::start_tag('li', array('class' => 'access-button'));
                $content .= html_writer::tag('a', get_string('char', 'block_accessibility'), array('id' => 'block_accessibility_reset', 'class' => 'outer right', 'title' => get_string('resettext', 'block_accessibility'), 'href' => $size_url->out(FALSE, array('op' => 'reset'))));
            $content .= html_writer::end_tag('li');

            $content .= html_writer::start_tag('li', array('class' => 'access-button'));
                $content .= html_writer::tag('a', get_string('char', 'block_accessibility').'+', $inc_attrs);
            $content .= html_writer::end_tag('li');

            $content .= html_writer::start_tag('li', array('class' => 'access-button'));
                $content .= html_writer::start_tag('a', $save_attrs);
                    $content .= html_writer::empty_tag('img', array('class' => 'inner', 'src' => $saveicon_url->out(false)));
                $content .= html_writer::end_tag('a');
            $content .= html_writer::end_tag('li');

        $content .= html_writer::end_tag('ul');

        // Colour change buttons
        $content .= html_writer::start_tag('ul', array('id' => 'block_accessibility_changecolour'));

        $content .= html_writer::start_tag('li', array('class' => 'access-button'));
            $content .= html_writer::tag('a', get_string('char', 'block_accessibility'), array('id' => 'block_accessibility_colour1', 'class' => 'outer row', 'href' => $colour_url->out(false, array('scheme' => 1))));
        $content .= html_writer::end_tag('li');

        $content .= html_writer::start_tag('li', array('class' => 'access-button'));
            $content .= html_writer::tag('a', get_string('char', 'block_accessibility'), array('id' => 'block_accessibility_colour2', 'class' => 'outer row right', 'href' => $colour_url->out(false, array('scheme' => 2))));
        $content .= html_writer::end_tag('li');

        $content .= html_writer::start_tag('li', array('class' => 'access-button'));
            $content .= html_writer::tag('a', get_string('char', 'block_accessibility'), array('id' => 'block_accessibility_colour3', 'class' => 'outer row right', 'href' => $colour_url->out(false, array('scheme' => 3))));
        $content .= html_writer::end_tag('li');

        $content .= html_writer::start_tag('li', array('class' => 'access-button'));
            $content .= html_writer::tag('a', get_string('char', 'block_accessibility'), array('id' => 'block_accessibility_colour4', 'class' => 'outer row right', 'href' => $colour_url->out(false, array('scheme' => 4))));
        $content .= html_writer::end_tag('li');

        $content .= html_writer::end_tag('ul');

        if (isset($USER->accessabilitymsg)) {
            $message = $USER->accessabilitymsg;
            unset($USER->accessabilitymsg);
        } else {
            $message = '';
        }
        $content .= html_writer::tag('div', $message, array('id' => 'block_accessibility_message', 'class' => 'clearfix'));

        $options = $DB->get_record('accessibility', array('userid' => $USER->id));
        $checkbox_attributes = array('type' => 'checkbox', 'value' => 1, 'id' => 'atbar_auto', 'name' => 'atbar_auto', 'class' => 'atbar_control');
        if ($options && $options->autoload_atbar) {
            $checkbox_attributes['checked'] = 'checked';
            $jsdata = array(
                'autoload_atbar' => true
            );
        } else {
            $jsdata = array(
                'autoload_atbar' => false
            );
        }
        // ATbar launch button (if javascript is enabled);
        $content .= html_writer::empty_tag('input', array('type' => 'button', 'value' => get_string('launchtoolbar', 'block_accessibility'), 'id' => 'block_accessibility_launchtoolbar', 'class' => 'atbar_control'));
        $content .= html_writer::empty_tag('input', $checkbox_attributes);
        $content .= html_writer::tag('label', get_string('autolaunch', 'block_accessibility'), array('for' => 'atbar_auto', 'class' => 'atbar_control'));

        $this->content->footer = '';
        $this->content->text = $content;

        $jsmodule = array(
            'name'  =>  'block_accessibility',
            'fullpath'  =>  '/blocks/accessibility/module.js',
            'requires'  =>  array('base', 'node', 'stylesheet')
        );


        if ($options && $options->autoload_atbar) {
            $jsdata = array(
                'autoload_atbar' => true
            );
        } else {
            $jsdata = array(
                'autoload_atbar' => false
            );
        }

        $this->page->requires->string_for_js('saved', 'block_accessibility');
        $this->page->requires->string_for_js('jsnosave', 'block_accessibility');
        $this->page->requires->string_for_js('reset', 'block_accessibility');
        $this->page->requires->string_for_js('jsnosizereset', 'block_accessibility');
        $this->page->requires->string_for_js('jsnocolourreset', 'block_accessibility');
        $this->page->requires->string_for_js('jsnosize', 'block_accessibility');
        $this->page->requires->string_for_js('jsnocolour', 'block_accessibility');
        $this->page->requires->string_for_js('jsnosizereset', 'block_accessibility');
        $this->page->requires->string_for_js('launchtoolbar', 'block_accessibility');
        $this->page->requires->js_init_call('M.block_accessibility.init', $jsdata, false, $jsmodule);

        return $this->content;
    }

    /**
     * Periodically clear the TTS cache so it doesn't get out of hand.
     */
    function cron() {
        global $CFG;
        $count = 0;
        $count += $this->clear_old_cache($CFG->dirroot.'/blocks/accessibility/toolbar/server/TTS/cache/chunks');
        $count += $this->clear_old_cache($CFG->dirroot.'/blocks/accessibility/toolbar/server/TTS/cache');
        mtrace(get_string('clearedoldcache', 'block_accessibility', $count));

    }

    /**
     * Passes over the given directory and deletes all files over an hour old.
     *
     * @param string $path The Directory to delete from
     * @return int Number of files deleted.
     */
    function clear_old_cache($path) {
        $dh = opendir($path);
        $count = 0;
        while (false !== ($file = readdir($dh))) {
            $stat = stat($path.'/'.$file);
            if (is_file($path.'/'.$file) && $stat['mtime'] < time()-3600 && $file != 'index.html') {
                unlink($path.'/'.$file);
                $count++;
            }
        }
        return $count;
    }
}
?>
