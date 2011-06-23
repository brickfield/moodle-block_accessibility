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
 * Defines the block_accessibility class                               (1)
 *
 * This file displays a block containing common accessibility tools,
 * such as buttons to change the size of the text. It also includes a
 * button to save settings, allowing them to persist between
 * pages/sessions.
 * For this to work, we need to add a one-line patch to the header.html
 * files of any themes we use on the site. Place the following somewhere
 * between <head> and </head>:
 * <!--  one-line patch to allow custom font sizes from block_accessibility --><link title="access_stylesheet" rel="stylesheet" href="<?php if($CFG->wwwroot != $CFG->httpswwwroot) {echo $CFG->httpswwwroot;} else {echo $CFG->wwwroot;} ?>/blocks/accessibility/userstyles.php" type="text/css" />
 *                                                                     (2)
 *
 * @package   blocks-accessibility                                      (3)
 * @copyright Copyright &copy; 2009 Taunton's College                   (4)
 * @author  Mark Johnson                                                (5)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v2 or later (6)
 */

require_once($CFG->dirroot.'/blocks/accessibility/lib.php');

class block_accessibility extends block_base {

    function init() {
        $this->title = get_string('blockname', 'block_accessibility');
        $this->version = 2009082600;
    }

    function get_content() {
        if ($this->content !== NULL) {
            return $this->content;
        }
        global $CFG, $USER;

        $this->content->text .= '<div id="textresize">';
        if(isset($USER->fontsize) && accessibility_getsize($USER->fontsize) == 10) {
            // Check if the decrease button should be disabled, i.e. we can't decrease any further (apply 'disabled' class and remove href if so)
            $this->content->text .= '<a title="'.get_string('dectext', 'block_accessibility').'" id="dec" class="outer disabled">';
        } else {
            $this->content->text .= '<a href="'.$CFG->wwwroot.'/blocks/accessibility/changesize.php?op=dec&amp;redirect='.$_SERVER['REQUEST_URI'].'" title="'.get_string('dectext', 'block_accessibility').'" id="dec" class="outer">';
        }
        $this->content->text .=     '   <div class="middle">
                                            <div class="inner">'.get_string('char', 'block_accessibility').'-</div>
                                        </div>
                                    </a>
                                    <a href="'.$CFG->wwwroot.'/blocks/accessibility/changesize.php?op=reset&amp;redirect='.$_SERVER['REQUEST_URI'].'" title="'.get_string('resettext', 'block_accessibility').'" id="reset" class="outer right">
                                        <div class="middle">
                                            <div class="inner">'.get_string('char', 'block_accessibility').'</div>
                                        </div>
                                    </a>';
        if(isset($USER->fontsize) && accessibility_getsize($USER->fontsize) == 26) {
            // Check if the increase button should be disabled, i.e. we can't increase any further
            $this->content->text .= '<a title="'.get_string('inctext', 'block_accessibility').'" id="inc" class="outer disabled right">';
        } else {
            $this->content->text .= '<a href="'.$CFG->wwwroot.'/blocks/accessibility/changesize.php?op=inc&amp;redirect='.$_SERVER['REQUEST_URI'].'" title="'.get_string('inctext', 'block_accessibility').'" id="inc" class="outer right">';
        }
        $this->content->text .=     '   <div class="middle">
                                            <div class="inner">'.get_string('char', 'block_accessibility').'+</div>
                                        </div>
                                    </a>';
        if(isset($USER->fontsize) || isset($USER->colourscheme)) {
            // Check if the save button should be disabled, i.e. there's no setting in $USER->fontsize that we can save
            $this->content->text .= '<a id="save" href="'.$CFG->wwwroot.'/blocks/accessibility/database.php?op=save&amp;size=true&amp;scheme=true&amp;redirect='.$_SERVER['REQUEST_URI'].'" class="outer right" title="'.get_string('save', 'block_accessibility').'">';
        } else {
            $this->content->text .= '<a id="save" class="outer disabled right" title="'.get_string('save', 'block_accessibility').'">';
        }
        $this->content->text .=     '   <div class="middle">
                                            <div class="inner">
                                                <img id="saveicon" src="'.$CFG->wwwroot.'/blocks/accessibility/pix/document-save';
        if(!isset($USER->fontsize) && !isset($USER->colourscheme)) {
            $this->content->text .=            '-grey';
        }
        $this->content->text .=                 '.png" />
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div id="colourchange">
                                    <a href="'.$CFG->wwwroot.'/blocks/accessibility/changecolour.php?scheme=1&amp;redirect='.$_SERVER['REQUEST_URI'].'" title="'.get_string('col1text', 'block_accessibility').'" id="colour1" class="outer row">
                                        <div class="middle">
                                            <div class="inner">'.get_string('char', 'block_accessibility').'</div>
                                        </div>
                                    </a>
                                    <a href="'.$CFG->wwwroot.'/blocks/accessibility/changecolour.php?scheme=2&amp;redirect='.$_SERVER['REQUEST_URI'].'" title="'.get_string('col2text', 'block_accessibility').'" id="colour2" class="outer row right">
                                        <div class="middle">
                                            <div class="inner">'.get_string('char', 'block_accessibility').'</div>
                                        </div>
                                    </a>
                                    <a href="'.$CFG->wwwroot.'/blocks/accessibility/changecolour.php?scheme=3&amp;redirect='.$_SERVER['REQUEST_URI'].'" title="'.get_string('col3text', 'block_accessibility').'" id="colour3" class="outer row right">
                                        <div class="middle">
                                            <div class="inner">'.get_string('char', 'block_accessibility').'</div>
                                        </div>
                                    </a>
                                    <a href="'.$CFG->wwwroot.'/blocks/accessibility/changecolour.php?scheme=4&amp;redirect='.$_SERVER['REQUEST_URI'].'" title="'.get_string('col4text', 'block_accessibility').'" id="colour4" class="outer row right">
                                        <div class="middle">
                                            <div class="inner">'.get_string('char', 'block_accessibility').'</div>
                                        </div>
                                    </a>
                                </div>
                                <div style="clear:both;"></div>';

        require_js(array(
            'yui_yahoo',
            'yui_event',
            'yui_dom',
            'yui_connection',
            'yui_selector',
            $CFG->wwwroot.'/blocks/accessibility/local/accessibility.js.php'));

        // setup a couple of global javascript variables, and set the javascript's init function to run with body.onload
        $this->content->text .= '<script type="text/javascript">/* <![CDATA[ */var stylesheet; var fontrule = null; var colourrules = Array();var webroot="'.$CFG->wwwroot.'";YAHOO.util.Event.on(document.body, "load", accessibility_init()); /* ]]> */</script>';

        $this->content->footer = '<div id="accessibility_message">';
        if(isset($USER->accessabilitymsg)) { // If there's a message to display, show it in the footer.
            $this->content->footer .= $USER->accessabilitymsg;
            unset($USER->accessabilitymsg);
        }
        $this->content->footer .= '</div>';

        return $this->content;

    }

}

?>