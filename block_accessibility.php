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

	CONST JS_URL = '/blocks/accessibility/module.js';
 	CONST CSS_URL = '/blocks/accessibility/userstyles.php';
 	CONST FONTSIZE_URL = '/blocks/accessibility/changesize.php';
	CONST COLOUR_URL = '/blocks/accessibility/changecolour.php';
	CONST DB_URL = '/blocks/accessibility/database.php';
	//CONST LOADER_ICON = '/blocks/accessibility/pix/loader.gif'; // not in use anymore

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
		$this->title = get_string('pluginname', 'block_accessibility');
	}

	/**
	 * Called after init(). Here we have instance id so we can use config for specific instance
	 * The function will include CSS declarations into Moodle Page
	 * CSS declarations will be generated according to user settings and instance configuration
	 *
	 */
	public function specialization(){
		$instance_id = $this->instance->id;

		
		if (!$this->page->requires->is_head_done()){

			// link default/saved settings to a page
			// each block instance has it's own configuration form, so we need instance id
			$cssurl = self::CSS_URL.'?instance_id='.$instance_id;
			$this->page->requires->css($cssurl);
		}
	}

	/**
	 * instance_allow_multiple explicitly tells there cannot be multiple
	 * block instance on the same page
	 *
	 */
	public function instance_allow_multiple(){
		return FALSE;
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


		// Until Issue #63 is fixed, we don't want to display block for unauthenticated users
		if(!isloggedin()) return null;

		if ($this->content !== null) {
			return $this->content;
		}

		/* this is not implemented yet
		// autosave block's config option
		$autosave = DEFAULT_AUTOSAVE; // by default
		if(isset($this->config->autosave)) $autosave = $this->config->autosave;
		*/

		// get the current page url (redirection after action when no Javascript)
		$params = array('redirect' => $FULLME);

		// set block services paths: changesize.php, changecolour.php and database.php
		$size_url = new moodle_url(self::FONTSIZE_URL, $params);
		$colour_url = new moodle_url(self::COLOUR_URL, $params);

		$params['op'] = 'save';
		$params['size'] = true;
		$params['scheme'] = true;
		$db_url = new moodle_url(self::DB_URL, $params);

		// INITIALIZE BUTTON ATTRIBUTES
		// ===============================================
		// initialization of increase_font, decrease_font and save button
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
			'id' => "block_accessibility_save",
			'href' => $db_url->out(false)
		);

		// initialization of reset button
		$reset_attrs = array(
			'id' => 'block_accessibility_reset',
			'title' => get_string('resettext', 'block_accessibility'),
			'href' => $size_url->out(false, array('op' => 'reset'))
		);


		// if any of increase/decrease buttons reached maximum size, disable it
		if (isset($USER->fontsize)) {
			if ($USER->fontsize == MIN_FONTSIZE) {
				$dec_attrs['class'] = 'disabled';
				unset($dec_attrs['href']);
			}
			if ($USER->fontsize == MAX_FONTSIZE) {
				$inc_attrs['class'] = 'disabled';
				unset($inc_attrs['href']);
			}
		}
		// or disable reset button
		else{
			$reset_attrs['class'] = 'disabled';
		}

		// if user is not logged in, disable save button
		// UPDATE 18.9.2014. Anyway the block will not be displayed for nonauthenticated users
		/*
		if (isset($USER->username) && (isset($USER->fontsize) || isset($USER->colourscheme))) {
			$save_attrs['href'] = $db_url->out(false);
		} else {
			$save_attrs['class'] = 'disabled';
		}
		*/


		// initialization of scheme profiles buttons
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
		if (!isset($USER->colourscheme)) {
			$c1_attrs['class'] = 'disabled';
		}


		// display:inline-block CSS declaration is not applied to block's buttons because IE7 doesn't support it. float is used insted for IE7 only
		$clearfix = '';
		if(preg_match('/(?i)msie [1-7]/',$_SERVER['HTTP_USER_AGENT'])){
			$clearfix = html_writer::tag('div', '', array('style'=>'clear:both')); // required for IE7
		}

		// RENDER BLOCK HTML
		// ===============================================
		$content = '';

		$strchar = get_string('char', 'block_accessibility');
		$resetchar = "R";
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
		
		$content .= $clearfix;


		// Colour change buttons
		$content .= html_writer::start_tag('ul', array('id' => 'block_accessibility_changecolour'));

		$content .= html_writer::start_tag('li', array('class' => 'access-button'));
		$content .= html_writer::tag('a', $resetchar, $c1_attrs);
		$content .= html_writer::end_tag('li');

		$content .= html_writer::start_tag('li', array('class' => 'access-button'));
		$content .= html_writer::tag('a', $strchar, $c2_attrs);
		$content .= html_writer::end_tag('li');

		$content .= html_writer::start_tag('li', array('class' => 'access-button'));
		$content .= html_writer::tag('a', $strchar, $c3_attrs);
		$content .= html_writer::end_tag('li');

		$content .= html_writer::start_tag('li', array('class' => 'access-button'));
		$content .= html_writer::tag('a', $strchar, $c4_attrs);
		$content .= html_writer::end_tag('li');

		$content .= html_writer::end_tag('ul');

		$content .= $clearfix;



		// e.g. "settings saved" or etc.
		if (isset($USER->accessabilitymsg)) {
			$message = $USER->accessabilitymsg;
			unset($USER->accessabilitymsg);
		} else {
			$message = '';
		}
		$messageattrs = array('id' => 'block_accessibility_message', 'class' => 'clearfix');
		$content .= html_writer::tag('div', $message, $messageattrs);

		// data to pass to module.js
		$jsdata['autoload_atbar'] = false;
		$jsdata['instance_id'] = $this->instance->id;

		// RENDER ATBAR
		// ===============================================
		$showATbar = DEFAULT_SHOWATBAR; // by default
		if(isset($this->config->showATbar)) $showATbar = $this->config->showATbar;

		if($showATbar){


			// load database record for a current user
			$options = $DB->get_record('block_accessibility', array('userid' => $USER->id));

			// initialize ATBar 
			$checkbox_attrs = array(
				'type' => 'checkbox',
				'value' => 1,
				'id' => 'atbar_auto',
				'name' => 'atbar_auto',
				'class' => 'atbar_control',
			);

			$label_attrs = array(
				'for' => 'atbar_auto',
				'class' => 'atbar_control'
			);


			if ($options && $options->autoload_atbar) {
				$checkbox_attrs['checked'] = 'checked';
				$jsdata['autoload_atbar'] = true;
			} 

			// ATbar launch button (if javascript is enabled)
			$launch_attrs = array(
				'type' => 'button',
				'value' => get_string('launchtoolbar', 'block_accessibility'),
				'id' => 'block_accessibility_launchtoolbar',
				'class' => 'atbar_control access-button'
			);

			// render ATBar
			$content .= html_writer::empty_tag('input', $launch_attrs);

			$spanattrs = array('class' => 'atbar-always');
			$content .= html_writer::start_tag('span', $spanattrs);
			$content .= html_writer::empty_tag('input', $checkbox_attrs);
			$strlaunch = get_string('autolaunch', 'block_accessibility');
			$content .= html_writer::tag('label', $strlaunch, $label_attrs);
			$content .= html_writer::end_tag('span');
		}

		$content .= html_writer::end_tag('div');

		// loader icon
		// html_writer::empty_tag('img', array('src' => new moodle_url(self::LOADER_ICON), 'alt' => 'loader icon')); // it doesn't work so it's implemented by CSS
		$spanattrs = array('id' => 'loader-icon');
		$content .= html_writer::start_tag('span', $spanattrs);
		$content .= html_writer::end_tag('span');

		$content .= $clearfix;




		// SET THE BLOCK CONTENT
		// ===============================================
		$this->content = new stdClass;
		$this->content->footer = '';
		$this->content->text = $content;


		// keep in mind that dynamic AJAX mode cannot work properly with IE <= 8 (for now), so javascript will not even be loaded
		if(!preg_match('/(?i)msie [1-8]/',$_SERVER['HTTP_USER_AGENT']))
		{
			// INCLUDE JS AND PASS PARAMETERS
			// ===============================================
			// language strings to pass to module.js
			$this->page->requires->string_for_js('saved', 'block_accessibility');
			$this->page->requires->string_for_js('jsnosave', 'block_accessibility');
			$this->page->requires->string_for_js('reset', 'block_accessibility');
			$this->page->requires->string_for_js('jsnosizereset', 'block_accessibility');
			$this->page->requires->string_for_js('jsnocolourreset', 'block_accessibility');
			$this->page->requires->string_for_js('jsnosize', 'block_accessibility');
			$this->page->requires->string_for_js('jsnocolour', 'block_accessibility');
			$this->page->requires->string_for_js('jsnosizereset', 'block_accessibility');
			$this->page->requires->string_for_js('jsnotloggedin', 'block_accessibility');
			$this->page->requires->string_for_js('launchtoolbar', 'block_accessibility');

			$jsmodule = array(
				'name'  =>  'block_accessibility',
				'fullpath'  =>  self::JS_URL,
				'requires'  =>  array('base', 'node', 'stylesheet')
			);

			// include js script and pass the arguments
			$this->page->requires->js_init_call('M.block_accessibility.init', $jsdata, false, $jsmodule);
		}
		

		return $this->content;
	}

}
