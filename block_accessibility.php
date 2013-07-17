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
 * @package    block_accessibility
 * @author      Mark Johnson <mark.johnson@tauntons.ac.uk>
 * @copyright   2010 Tauntons College, UK
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/blocks/accessibility/lib.php');

/**
 * accessibility Block's class
 */
class block_accessibility extends block_base {

    /**
     * Set the title and include the stylesheet
     *
     * We need to include the stylesheet here rather than in {@see get_content()} since get_content
     * is sometimes called after $OUTPUT->heading(), e.g. such as /user/index.php where the middle
     * region is hard-coded.
     * However, /admin/plugins.php calls init() for each plugin after $OUTPUT->heading(), so the
     * sheet is not included at all on that page.
     */
    public function init() {
        global $PAGE;
        $this->title = get_string('pluginname', 'block_accessibility');
        
        if (!$PAGE->requires->is_head_done()){
            $cssurl = '/blocks/accessibility/userstyles.php';
            $PAGE->requires->css($cssurl);
        }
    }

    /**
     * Set where the block should be allowed to be added
     *
     * @return array
     */
    public function applicable_formats() {
        return array('all' => true);
    }

    /**
     * Generate the contents for the block
     *
     * @return object Block contents and footer
     */
    public function get_content () {
        global $CFG;
        global $USER;
        global $FULLME;
        global $DB;
        if ($this->content !== null) {
            return $this->content;
        }

        $params = array('redirect' => $FULLME);
        $size_url = new moodle_url('/blocks/accessibility/changesize.php', $params);
        $colour_url = new moodle_url('/blocks/accessibility/changecolour.php', $params);
        $params['op'] = 'save';
        $params['size'] = true;
        $params['scheme'] = true;
        $db_url = new moodle_url('/blocks/accessibility/database.php', $params);

        $inc_attrs = array(
            'title' => get_string('inctext', 'block_accessibility'),
            'id' => "block_accessibility_inc",
            'href' => $size_url->out(false, array('op' => 'inc'))
        );
        $dec_attrs = array(
            'title' => get_string('dectext', 'block_accessibility'),
            'id' => "block_accessibility_dec",
            'href' => $size_url->out(false, array('op' => 'dec'))
        );
        $save_attrs = array(
            'title' => get_string('save', 'block_accessibility'),
            'id' => "block_accessibility_save"
        );

        if (isset($USER->fontsize)) {
            if (accessibility_getsize($USER->fontsize) == 10) {
                $dec_attrs['class'] = 'disabled';
                unset($dec_attrs['href']);
            }
            if (accessibility_getsize($USER->fontsize) == 26) {
                $inc_attrs['class'] = 'disabled';
                unset($inc_attrs['href']);
            }
        }
        if (isset($USER->username) && (isset($USER->fontsize) || isset($USER->colourscheme))) {
            $save_attrs['href'] = $db_url->out(false);
        } else {
            $save_attrs['class'] = 'disabled';
        }

        $reset_attrs = array(
            'id' => 'block_accessibility_reset',
            'title' => get_string('resettext', 'block_accessibility'),
            'href' => $size_url->out(false, array('op' => 'reset'))
        );

        $c1_attrs = array(
            'id' => 'block_accessibility_colour1',
            'href' => $colour_url->out(false, array('scheme' => 1))
        );
        $c2_attrs = array(
            'id' => 'block_accessibility_colour2',
            'href' => $colour_url->out(false, array('scheme' => 2))
        );
        $c3_attrs = array(
            'id' => 'block_accessibility_colour3',
            'href' => $colour_url->out(false, array('scheme' => 3))
        );
        $c4_attrs = array(
            'id' => 'block_accessibility_colour4',
            'href' => $colour_url->out(false, array('scheme' => 4))
        );

        $content = '';

        $strchar = get_string('char', 'block_accessibility');
        $divattrs = array('id' => 'accessibility_controls', 'class' => 'content');
        $listattrs = array('id' => 'block_accessibility_textresize', 'class' => 'button_row');

        $content .= html_writer::start_tag('div', $divattrs);
        $content .= html_writer::start_tag('ul', $listattrs);

        $content .= html_writer::start_tag('li', array('class' => 'access-button'));
        $content .= html_writer::tag('a', $strchar.'-', $dec_attrs);
        $content .= html_writer::end_tag('li');

        $content .= html_writer::start_tag('li', array('class' => 'access-button'));
        $content .= html_writer::tag('a', $strchar, $reset_attrs);
        $content .= html_writer::end_tag('li');

        $content .= html_writer::start_tag('li', array('class' => 'access-button'));
        $content .= html_writer::tag('a', $strchar.'+', $inc_attrs);
        $content .= html_writer::end_tag('li');

        $content .= html_writer::start_tag('li', array('class' => 'access-button'));
        $content .= html_writer::tag('a', '&nbsp', $save_attrs);
        $content .= html_writer::end_tag('li');

        $content .= html_writer::end_tag('ul');

        // Colour change buttons
        $content .= html_writer::start_tag('ul', array('id' => 'block_accessibility_changecolour'));

        $content .= html_writer::start_tag('li', array('class' => 'access-button'));
        $content .= html_writer::tag('a', get_string('char', 'block_accessibility'), $c1_attrs);
        $content .= html_writer::end_tag('li');

        $content .= html_writer::start_tag('li', array('class' => 'access-button'));
        $content .= html_writer::tag('a', get_string('char', 'block_accessibility'), $c2_attrs);
        $content .= html_writer::end_tag('li');

        $content .= html_writer::start_tag('li', array('class' => 'access-button'));
        $content .= html_writer::tag('a', get_string('char', 'block_accessibility'), $c3_attrs);
        $content .= html_writer::end_tag('li');

        $content .= html_writer::start_tag('li', array('class' => 'access-button'));
        $content .= html_writer::tag('a', get_string('char', 'block_accessibility'), $c4_attrs);
        $content .= html_writer::end_tag('li');

        $content .= html_writer::end_tag('ul');

        $content .= html_writer::end_tag('div');
        if (isset($USER->accessabilitymsg)) {
            $message = $USER->accessabilitymsg;
            unset($USER->accessabilitymsg);
        } else {
            $message = '';
        }

        $messageattrs = array('id' => 'block_accessibility_message', 'class' => 'clearfix');
        $content .= html_writer::tag('div', $message, $messageattrs);

        $options = $DB->get_record('block_accessibility', array('userid' => $USER->id));

        $checkbox_attrs = array(
            'type' => 'checkbox',
            'value' => 1,
            'id' => 'atbar_auto',
            'name' => 'atbar_auto',
            'class' => 'atbar_control'
        );

        $label_attrs = array(
            'for' => 'atbar_auto',
            'class' => 'atbar_control'
        );

        if ($options && $options->autoload_atbar) {
            $checkbox_attrs['checked'] = 'checked';
            $jsdata = array(
                'autoload_atbar' => true
            );
        } else {
            $jsdata = array(
                'autoload_atbar' => false
            );
        }
        // ATbar launch button (if javascript is enabled);
        $launch_attrs = array(
            'type' => 'button',
            'value' => get_string('launchtoolbar', 'block_accessibility'),
            'id' => 'block_accessibility_launchtoolbar',
            'class' => 'atbar_control'
        );

        $content .= html_writer::empty_tag('input', $launch_attrs);
        $content .= html_writer::empty_tag('input', $checkbox_attrs);
        $strlaunch = get_string('autolaunch', 'block_accessibility');
        $content .= html_writer::tag('label', $strlaunch, $label_attrs);

        $this->content = new stdClass;
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
        $this->page->requires->js_init_call('M.block_accessibility.init',
                                            $jsdata,
                                            false,
                                            $jsmodule);

        return $this->content;
    }

}
