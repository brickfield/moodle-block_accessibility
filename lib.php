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
 * Defines 2 functions used in the block
 *
 * 2 functions are defined here. accessibility_getsize() which
 * converts the current textsize between px and %, and
 * accessibility_is_ajax() which finds out if we're responding
 * to an AJAX request.
 *
 * @package   block_accessibility
 * @copyright Copyright 2009 onwards Taunton's College
 * @author  Mark Johnson <mark.johnson@tauntons.ac.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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

// Block constants.
// ! this definitions might cause conflicts to other Moodle plugins if there is used the same name.
define('DEFAULT_FONTSIZE', 100); // In %.
define('MAX_FONTSIZE', 197); // In %.
define('MIN_FONTSIZE', 77); // In %.
define('MAX_PX_FONTSIZE', 26); // In px.
define('MIN_PX_FONTSIZE', 10); // In px.

define('DEFAULT_SHOWATBAR', true);
define('DEFAULT_AUTOSAVE', false);

/**
 * Compares the size in px against the size as %.
 *
 * @param int $size
 * @return mixed
 */
function accessibility_getsize($size) {

    // Define the array of sizes in px against sizes as %
    // make sure to maintain defined constants above in the script
    // http://yuilibrary.com/yui/docs/cssfonts/.
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
    if (is_int($size) && array_key_exists($size, $sizes)) { // If we're looking at a key (px).
        return $sizes[$size]; // Return the value (%).
    } else {
        if (in_array($size, $sizes)) { // If we're looking at a value (%).
            return array_search($size, $sizes); // Return the key (px).
        } else {
            throw new moodle_exception('invalidsize', 'block_accessibility');
        }
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

/**
 * To use by Ajax PHP scripts. Ensures that execeptions thrown by the site policy requirements don't abort the scripts. This allows
 * font changes to be used by non logged in users and users that have not yet agreed to the policy.
 */
function block_accessibility_require_login() {
    try {
        // We expect an exception here if the user didn't agree to the site policy yet, but we will only get the exception if
        // redirecting is not allowed (fifth parameter set to true). This should be fine as this function is called by a block, and
        // page will have already called require_login and redirected to the site policy page.
        // This catch is for the site policy page only.
        require_login(null, true, null, true, true);
    } catch (Exception $e) {
        // We are expecting only a sitepolicynotagreed exception. Swallow it, and throw all others.
        if (!($e instanceof moodle_exception) or $e->errorcode != 'sitepolicynotagreed') {
            // In case we receive a different exception, throw it.
            throw $e;
        }
    }
}
