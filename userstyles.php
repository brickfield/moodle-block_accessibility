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
 * Sets per-user styles (all the CSS declarations are here)            (1)
 *
 * This file is the cornerstone of the block - when the page loads, it
 * checks if the user has a custom settings for the font size and colour
 * scheme (either in the session or the database) and creates a stylesheet
 * to override the standard styles with this setting.                  (2)
 *
 * @see block_accessibility.php                                        (3)
 * @package   block_accessibility                                      (4)
 * @copyright Copyright 2009 onwards Taunton's College                   (5)
 * @author Mark Johnson <mark.johnson@tauntons.ac.uk>                  (6)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later (7)
 */

header('Content-Type: text/css', true);
header("X-Content-Type-Options: nosniff"); // for IE
//header('Cache-Control: no-cache');


require_once('../../config.php');

if (!isloggedin()) die();

/* Get block instance config data outside of it's class
   https://moodle.org/mod/forum/discuss.php?d=129799
   Also check configdata encoding in C:\...\blocks\moodleblock.class.php
*/
$instance_id = required_param('instance_id', PARAM_INT);
$data = $DB->get_record('block_instances', array('id' => $instance_id), '*', MUST_EXIST); 
$block_instance = block_instance('accessibility', $data); // test it in all languages?
// if (!$block_instance) error...



// READ USER SETTINGS
// ================================================
// First, check the session to see if the user's overridden the default/saved setting
$options = $DB->get_record('block_accessibility', array('userid' => $USER->id));

// NOTE: User settings priority: 1. $USER session, 2. database
// check for fontsize user setting
if (!empty($USER->fontsize)) $fontsize = $USER->fontsize;
else if (!empty($options->fontsize)) $fontsize = $options->fontsize;

// check for colourscheme user setting
if (!empty($USER->colourscheme)) $colourscheme = $USER->colourscheme;
else if (!empty($options->colourscheme)) $colourscheme = $options->colourscheme;


// FONT SIZE CSS DECLARATIONS
// ================================================
// Echo out CSS for the body element. Use !important to override any other external stylesheets.
if (!empty($fontsize)) {
	echo '
	#page { /* block elements */
		font-size: '.$fontsize.'% !important;
		line-height:1.5; /*WCAG 2.0*/
	}
	#page *{
		font-size: inherit !important;
		line-height: inherit !important;
	}
	';
}


// COLOUR SCHEMES CSS DECLARATIONS
// ================================================
/*
	So far, selector * is used. This might cause some problems. Idea: Maybe better solution is to apply backgrounds to specific elements like body, .header, ...
*/
if (!empty($colourscheme)) {
	// $colourscheme == 1 is reset, so don't output any styles
	if($colourscheme > 1 && $colourscheme < 5){ // this is how many declarations we defined in edit_form.php
		if(!empty($block_instance->config)){
			$fg_colour = $block_instance->config->{'fg'.$colourscheme};
			$bg_colour = $block_instance->config->{'bg'.$colourscheme};
		}
		else{ // block has never been configured, load default colours
			require_once($CFG->dirroot.'/blocks/accessibility/defaults.php');
			$fg_colour = $defaults['fg'.$colourscheme];
			$bg_colour = $defaults['bg'.$colourscheme];
		}
		
	}

	// if no colours defined, no output, it will remain as default
	if(!empty($bg_colour)){ echo '
		forumpost .topic {
			background-image: none !important;
		}
		*:not([class*="mce"]):not([id*="mce"]):not([id*="editor"]){
			/* it works well only with * selector but mce editor gets unusable */
			background-color: '.$bg_colour.' !important;
			background-image: none !important;
			text-shadow:none !important;
		}
		';
	}

	// it is recommended not to change forground colour
	if(!empty($fg_colour)){ echo '
		*:not([class*="mce"]):not([id*="mce"]):not([id*="editor"]){
			/* it works well only with * selector but mce editor gets unusable */
			color: '.$fg_colour.' !important;
		}
		#content a, .tabrow0 span {
			color: '.$fg_colour.' !important;
		}
		.tabrow0 span:hover {
			text-decoration: underline;
		}
		.block_accessibility .outer {
			border-color: '.$bg_colour.' !important;
		}
		';
	}

	
}

// ACCESSIBILITY BLOCK'S COLOUR SCHEMES BUTTONS
// Do not edit (this part of code is not in styles.php because colours are defined in block's configuration form)
// ================================================
for($i=2; $i<5; $i++) {  // this is how many declarations we defined in defaults.php
	$colourscheme = $i;
	if(!empty($block_instance->config)){
		$fg_colour = $block_instance->config->{'fg'.$colourscheme};
		$bg_colour = $block_instance->config->{'bg'.$colourscheme};
	}
	else{ // block has never been configured, load default colours
		require_once($CFG->dirroot.'/blocks/accessibility/defaults.php');
		$fg_colour = $defaults['fg'.$colourscheme];
		$bg_colour = $defaults['bg'.$colourscheme];
	}
	echo '#block_accessibility_colour'.$colourscheme.'{';
	if(!empty($fg_colour)) echo 'color:'.$fg_colour.' !important;';
	if(!empty($bg_colour)) echo 'background-color:'.$bg_colour.' !important;';
	echo '}';


}

		
