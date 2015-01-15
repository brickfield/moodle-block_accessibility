<?php

// This settings will be available throughout the block.
// Usage example: $this->config->autosave

class block_accessibility_edit_form extends block_edit_form {
	
	CONST CNF_AUTOSAVE = 'config_autosave';
	CONST CNF_ATBAR = 'config_showATbar';
	CONST CNF_FG = 'config_fg'; // fg1, fg2,...
	CONST CNF_BG = 'config_bg'; // bg1, bg2,...

	CONST RE_COLOUR = '/^#[a-f0-9]{6}$/i';

	protected function specific_definition($mform) {	






		// load default colours
		global $CFG;
		require_once($CFG->dirroot.'/blocks/accessibility/defaults.php');
		
		/* not implemented, so far I'm not sure if it's going to be useful
		// auto-save user settings
		$mform->addElement('advcheckbox',self::CNF_AUTOSAVE,
			get_string (self::CNF_AUTOSAVE, 'block_accessibility'),
			get_string (self::CNF_AUTOSAVE.'_checkbox', 'block_accessibility' ),
			null,
			array (0, 1)
		);
		$mform->setDefault(self::CNF_AUTOSAVE, 0);
		$mform->setType (self::CNF_AUTOSAVE, PARAM_INT);
		$mform->addHelpButton(self::CNF_AUTOSAVE, self::CNF_AUTOSAVE, 'block_accessibility');
		*/

		// allow ATbar 
		$mform->addElement('advcheckbox', self::CNF_ATBAR,
			get_string (self::CNF_ATBAR, 'block_accessibility'),
			get_string (self::CNF_ATBAR.'_checkbox', 'block_accessibility'),
			null,
			array (0, 1)
		);
		$mform->setDefault(self::CNF_ATBAR, 1);
		$mform->setType (self::CNF_ATBAR, PARAM_INT);
		$mform->addHelpButton(self::CNF_ATBAR, self::CNF_ATBAR, 'block_accessibility');

		// An idea: put here default font-size setting?

		// colour schemes
		for($i=2; $i<5; $i++) {  // this is how many declarations we defined in defaults.php
			// get previously saved configuration
			$form = $this->block->config; // or cast it to (array) and get properties like with []
			$fg = str_replace('config_', '', self::CNF_FG);
			$bg = str_replace('config_', '', self::CNF_BG);
			$fg_colour = isset($form->{$fg.$i})? $form->{$fg.$i} : $defaults['fg'.$i];
			$bg_colour = isset($form->{$bg.$i})? $form->{$bg.$i} : $defaults['bg'.$i];

			// display scheme example and identifier number of a scheme
			$mform->addElement('html', '
			<div class="fitem" style="padding:10px 0 8px">
				<div class="fitemtitle"></div>
				<div class="felement">
					<span style="padding:2px 8px; color:'.$fg_colour.'; border:1px solid '.$fg_colour.'; background:'.$bg_colour.' !important">A</span>
					Colour scheme #'.$i.'
				</div>
			</div>');

			// foreground colour
			$id = self::CNF_FG.$i;
			$mform->addElement('text', $id, get_string(self::CNF_FG, 'block_accessibility'));
			$mform->setDefault($id, $defaults['fg'.$i]); 
			$mform->setType($id, PARAM_TEXT);
			$mform->addHelpButton($id, self::CNF_FG, 'block_accessibility');
			$mform->addRule($id, get_string('color_input_error', 'block_accessibility'), 'regex', self::RE_COLOUR, 'server', false, false);
			
			// background colour
			$id = self::CNF_BG.$i;
			$mform->addElement('text', $id, get_string(self::CNF_BG, 'block_accessibility'));
			$mform->setDefault($id, $defaults['bg'.$i]);
			$mform->setType($id, PARAM_TEXT);
			$mform->addHelpButton($id, self::CNF_BG, 'block_accessibility');
			$mform->addRule($id, get_string('color_input_error', 'block_accessibility'), 'regex', self::RE_COLOUR, 'server', false, false);

		}

		// if someone is willing to do settings form reset button, it would be helpful
			
		
		
	}
}