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

/**
 config.php overwrites header content-type in moodle 2.8
 */
require_once('../../config.php');

header('Content-Type: text/css', true);
header("X-Content-Type-Options: nosniff"); // for IE
header('Cache-Control: no-cache');

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
		line-height: inherit !important;
		font-size: inherit !important;
	}


	/* issue #74 - default h* sizes from Moodle CSS */
	#page #page-header h1, #page #region-main h1{font-size:'. (0.32 * $fontsize) .'px !important}
	#page #page-header h2, #page #region-main h2{font-size:'. (0.28 * $fontsize) .'px !important}
	#page #page-header h3, #page #region-main h3{font-size:'. (0.24 * $fontsize) .'px !important}
	#page #page-header h4, #page #region-main h4{font-size:'. (0.20 * $fontsize) .'px !important}
	#page #page-header h5, #page #region-main h5{font-size:'. (0.16 * $fontsize) .'px !important}
	#page #page-header h6, #page #region-main h6{font-size:'. (0.12 * $fontsize) .'px !important}
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
		if($block_instance->config !== NULL){
			$fg_colour = $block_instance->config->{'fg'.$colourscheme};
			$bg_colour = $block_instance->config->{'bg'.$colourscheme};
		}
		else{ // block has never been configured, load default colours
			require_once($CFG->dirroot.'/blocks/accessibility/defaults.php');
			$fg_colour = $defaults['fg'.$colourscheme];
			$bg_colour = $defaults['bg'.$colourscheme];
		}
		
	}


	// keep in mind that :not selector cannot work properly with IE <= 8 so this will not be included
	$not_selector_for_gteIE8 = '';
	if(!preg_match('/(?i)msie [1-8]/',$_SERVER['HTTP_USER_AGENT']))
	{
		$not_selector_for_gteIE8 = ':not([class*="mce"]):not([id*="mce"]):not([id*="editor"])';
	}

	// if no colours defined, no output, it will remain as default
	if(!empty($bg_colour)){ echo '
		forumpost .topic {
			background-image: none !important;
		}
		*'. $not_selector_for_gteIE8 .'{
			/* it works well only with * selector but mce editor gets unusable */
			background-color: '.$bg_colour.' !important;
			background-image: none !important;
			text-shadow:none !important;
		}
		';
	}

	// it is recommended not to change forground colour
	if(!empty($fg_colour)){ echo '
		*'. $not_selector_for_gteIE8 .'{
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
	if($block_instance->config !== NULL){
		//var_dump($block_instance->config->{'fg'.$colourscheme}, $block_instance->config->{'bg'.$colourscheme});
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
		
// display:inline-block CSS declaration is not applied to block's buttons because IE7 doesn't support it. float is used insted for IE7 only
if(preg_match('/(?i)msie [1-7]/',$_SERVER['HTTP_USER_AGENT']))
{
	echo '#accessibility_controls .access-button{float:left;}';
	echo '.atbar-always{float:left;}';
}
