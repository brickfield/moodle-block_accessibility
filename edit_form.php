<?php

// This settings will be available throughout the block. Example: $this->config->autosave
define('CNF_AUTOSAVE', 'config_autosave');
define('CNF_ATBAR', 'config_showATbar');
define('CNF_FG', 'config_fg'); // fg1, fg2,...
define('CNF_BG', 'config_bg'); // bg1, bg2,...

define('RE_COLOUR', '/^#[a-f0-9]{6}$/i');

class block_accessibility_edit_form extends block_edit_form {
	protected function specific_definition($mform) {	

		// load default colours
		global $CFG;
		require_once($CFG->dirroot.'/blocks/accessibility/defaults.php');
		
		
		/* this is not implemented yet
		// auto-save user settings
		$mform->addElement('advcheckbox',CNF_AUTOSAVE,
			get_string (CNF_AUTOSAVE, 'block_accessibility'),
			get_string (CNF_AUTOSAVE.'_checkbox', 'block_accessibility' ),
			null,
			array (0, 1)
		);
		$mform->setDefault(CNF_AUTOSAVE, 0);
		$mform->setType (CNF_AUTOSAVE, PARAM_INT);
		$mform->addHelpButton(CNF_AUTOSAVE, CNF_AUTOSAVE, 'block_accessibility');


		// allow ATbar 
		$mform->addElement('advcheckbox', CNF_ATBAR,
			get_string (CNF_ATBAR, 'block_accessibility'),
			get_string (CNF_ATBAR.'_checkbox', 'block_accessibility'),
			null,
			array (0, 1)
		);
		$mform->setDefault(CNF_ATBAR, 1);
		$mform->setType (CNF_ATBAR, PARAM_INT);
		$mform->addHelpButton(CNF_ATBAR, CNF_ATBAR, 'block_accessibility');

		// An idea: put here default font-size setting?
		*/

		// colour schemes
		for($i=2; $i<5; $i++) {  // this is how many declarations we defined in userstyles.php
			// get previously saved configuration
			$form = $this->block->config; // or cast it to (array) and get properties like with []
			$fg = str_replace('config_', '', CNF_FG);
			$bg = str_replace('config_', '', CNF_BG);
			$fg_colour = isset($form->{$fg.$i})? $form->{$fg.$i} : $defaults['fg'.$i];
			$bg_colour = isset($form->{$bg.$i})? $form->{$bg.$i} : $defaults['bg'.$i];

			// display scheme example and identifier number of a scheme
			$mform->addElement('html', '
			<div class="fitem" style="padding:10px 0 8px">
				<div class="fitemtitle"></div>
				<div class="felement">
					<span style="padding:2px 8px; color:'.$fg_colour.'; border:1px solid '.$fg_colour.'; background:'.$bg_colour.'">A</span>
					Colour scheme #'.$i.'
				</div>
			</div>');

			// foreground colour
			$id = CNF_FG.$i;
			$mform->addElement('text', $id, get_string(CNF_FG, 'block_accessibility'));
			$mform->setDefault($id, $defaults['fg'.$i]); 
			$mform->setType($id, PARAM_TEXT);
			$mform->addHelpButton($id, CNF_FG, 'block_accessibility');
			$mform->addRule($id, get_string('color_input_error', 'block_accessibility'), 'regex', RE_COLOUR, 'server', false, false);
			
			// background colour
			$id = CNF_BG.$i;
			$mform->addElement('text', $id, get_string(CNF_BG, 'block_accessibility'));
			$mform->setDefault($id, $defaults['bg'.$i]);
			$mform->setType($id, PARAM_TEXT);
			$mform->addHelpButton($id, CNF_BG, 'block_accessibility');
			$mform->addRule($id, get_string('color_input_error', 'block_accessibility'), 'regex', RE_COLOUR, 'server', false, false);

		}

		// if someone is willing to do reset button, it would be helpful
			
		
		
	}
}