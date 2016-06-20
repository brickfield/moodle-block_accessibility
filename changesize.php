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
 * Changes the text size via PHP or AJAX                               (1)
 *
 * This file finds the current font size, increases/decreases/resets it
 * as specified, and stores it in the $USER->fontsize session variable.
 * If requested via AJAX, it also returns the font size as a JSON
 * string or suiable error code. If not, it redirects the user back to
 * where they came from.                                               (2)
 *
 * @package   block_accessibility                                      (3)
 * @author      Mark Johnson <mark.johnson@tauntons.ac.uk>
 * @copyright   2010 Tauntons College, UK
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later (5)
 */

header('Cache-Control: no-cache');

require_once('../../config.php');
require_once($CFG->dirroot . '/blocks/accessibility/lib.php');
require_login();

// If the user hasn't already changed the size, we need to find a default/referent so we know where we're
// increasing/decreasing from.
if (!isset($USER->defaultfontsize)) {
    $USER->defaultfontsize = DEFAULT_FONTSIZE;
}

// Get the current font size in px.
// NOTE: User settings priority: 1. $USER session, 2. database, 3. default.
$current = $USER->defaultfontsize;
if (isset($USER->fontsize)) {
    $current = $USER->fontsize; // User session.
} else {
    if ($userstyle = $DB->get_record('block_accessibility', array('userid' => $USER->id))) {
        $current = $userstyle->fontsize; // User stored settings.
    }
}

// If value is in %, convert it to px to get array index of px so we are able to increment it...
if ($current > MAX_PX_FONTSIZE) { // Must be in % then.
    // If we're already dealing with a percentage...
    $current = accessibility_getsize($current); // Get the size in pixels.
}

// Ok, we have font size in px now, get new percentage value from it.

$op = required_param('op', PARAM_TEXT);
switch ($op) {
    case 'inc':
        if ($current == MAX_PX_FONTSIZE) {
            // If we're at the upper limit, don't increase any further.
            $new = accessibility_getsize($current);
        } else {
            // Otherwise, increase.
            $new = accessibility_getsize($current + 1);
        }
        break;
    case 'dec':
        if ($current == MIN_PX_FONTSIZE) {
            // If we're at the lower limit, don't decrease any further.
            $new = accessibility_getsize($current);
        } else {
            $new = accessibility_getsize($current - 1);
        }
        break;
    case 'reset':
        // Clear the fontsize stored in the session.
        unset($USER->fontsize);
        unset($USER->defaultfontsize);

        // Clear user records in database.
        $urlparams = array(
                'op' => 'reset',
                'size' => true,
                'userid' => $USER->id
        );
        if (!accessibility_is_ajax()) {
            $redirect = required_param('redirect', PARAM_TEXT);
            $urlparams['redirect'] = safe_redirect_url($redirect);
        }

        $redirecturl = new moodle_url('/blocks/accessibility/database.php', $urlparams);
        redirect($redirecturl);

        break;
    default:
        if (accessibility_is_ajax()) {
            header('HTTP/1.0 400 Bad Request');
        } else {
            print_error('invalidop', 'block_accessibility');
        }
        exit();
}

// Set the new font size in %
$USER->fontsize = $new; // If we've just increased or decreased, save the new size to the session.

if (!accessibility_is_ajax()) {
    // Otherwise, redirect the user
    // if action is not achieved through ajax, redirect back to page is required.
    $redirect = required_param('redirect', PARAM_TEXT);
    $redirecturl = new moodle_url($redirect);
    redirect($redirecturl);
}
