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
 * Defines 2 functions used in the block                               (1)
 *
 * 2 functions are defined here. {@link accessibility_getsize()} which
 * converts the current textsize between px and %, and
 * {@link accessibility_is_ajax()} which finds out if we're responding
 * to an AJAX request.                                                 (2)
 *
 * @package   block_accessibility                                      (3)
 * @copyright Copyright 2009 onwards Taunton's College                   (4)
 * @author  Mark Johnson <mark.johnson@tauntons.ac.uk>                 (5)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later (6)
 */

/**
 * Convert text size in pixels into a percentage eqivalent, or vice versa, accoring to the YUI
 * fonts CSS guidelines
 * http://developer.yahoo.com/yui/fonts/
 *
 * @param int|real $size the text size we're converting. Sizes between 10 and 26 will be treated
 *                       as pixel values. Sizes between 77 and 197 will be treated as percentage
 *                       values.
 * @return number the converted size
 */

defined('MOODLE_INTERNAL') || die();

// block constants
// ! this definitions might cause conflicts to other Moodle plugins if there is used the same name
define('DEFAULT_FONTSIZE', 100); // in %
define('MAX_FONTSIZE', 197); // in %
define('MIN_FONTSIZE', 77); // in %
define('MAX_PX_FONTSIZE', 26); // in px
define('MIN_PX_FONTSIZE', 10); // in px

define('DEFAULT_SHOWATBAR', TRUE);
define('DEFAULT_AUTOSAVE', FALSE);

function accessibility_getsize($size) {

    // Define the array of sizes in px against sizes as %
    // make sure to maintain defined constants above in the script
    // http://yuilibrary.com/yui/docs/cssfonts/
    $sizes = array(
        10 => 77,
        11 => 85,
        12 => 93,
        13 => 100,
        14 => 108,
        15 => 116,
        16 => 123.1,
        17 => 131,
        18 => 138.5,
        19 => 146.5,
        20 => 153.9,
        21 => 161.6,
        22 => 167,
        23 => 174,
        24 => 182,
        25 => 189,
        26 => 197
    );
    if (is_int($size) && array_key_exists($size, $sizes)) { // If we're looking at a key (px)
        return $sizes[$size]; // Return the value (%)
    } else if (in_array($size, $sizes)) { // If we're looking at a value (%)
        return array_search($size, $sizes); // Return the key (px)
    } else {
        throw new moodle_exception('invalidsize', 'block_accessibility');
    }
}

/**
 * Find out whether we're desponding to an AJAX call by seeing if the HTTP_X_REQUESTED_WITH header
 * is XMLHttpRequest
 *
 * @return boolean whether we're reponding to an AJAX call or not
 */
function accessibility_is_ajax() {
    $reqwith = 'HTTP_X_REQUESTED_WITH';
    if (isset($_SERVER[$reqwith]) && $_SERVER[$reqwith] == 'XMLHttpRequest') {
        $xhr = true;
    } else {
        $xhr = false;
    }
    return $xhr;
}
