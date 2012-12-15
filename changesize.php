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

require_once('../../config.php');
require_once($CFG->dirroot.'/blocks/accessibility/lib.php');
require_login();

header('Cache-Control: no-cache');
$op = required_param('op', PARAM_TEXT);
$cur = optional_param('cur', 0, PARAM_INT);

if (!accessibility_is_ajax()) {
    $redirect = required_param('redirect', PARAM_TEXT);
    $redirecturl = new moodle_url($redirect);
}

if (!isset($USER->defaultfontsize)) {
    if ($cur) {
        $USER->defaultfontsize = $cur;
    } else {
        $USER->defaultfontsize = 100;
    }
}

if (!isset($USER->fontsize)) {
    // If the user hasn't already changed the size, we need to find a default so we know where
    // we're increasing/decreasing from
    if ($userstyle = $DB->get_record('block_accessibility', array('userid' => $USER->id))) {
        // First, check the database to see if they've got a setting saved
        $current = $userstyle->fontsize;
    } else {
        // If not, use the current size from the page (if js in available).
        $current = $USER->defaultfontsize;
    }
} else {
    $current = $USER->fontsize;
}

if ($current >= 77 && $current <= 197) {
    // If we're already dealing with a percentage,
    $current = accessibility_getsize($current); // Get the size in pixels
}
switch($op) {
    case 'inc':
        if ($current == 26) {
            // If we're at the upper limit, don't increase any further.
            $new = accessibility_getsize($current);
        } else {
            // Otherwise, increase
            $new = accessibility_getsize($current+1);
        }
        break;
    case 'dec':
        if ($current == 10) {
            // If we're at the lower limit, don't decrease any further.
            $new = accessibility_getsize($current);
        } else {
            $new = accessibility_getsize($current-1);
        }
        break;
    case 'reset':
        unset($USER->fontsize); // Clear the fontsize stored in the session
        if (accessibility_is_ajax()) {
            // If we're responding to AJAX, send back the new font size to change to
            header('Content-Type: application/json');
            $output = new stdClass;
            $output->oldfontsize = accessibility_getsize($current);
            $output->fontsize = 100;
            echo(json_encode($output));
            exit();
        } else {
            // Otherwise, redirect the user
            $urlparams = array(
                'op' => 'reset',
                'size' => true,
                'userid' => $USER->id,
                'redirect' => $redirect
            );
            $redirecturl = new moodle_url('/blocks/accessibility/database.php', $urlparams);
            redirect($redirecturl);
        }
        break;
    default:
        if (accessibility_is_ajax()) {
            header('HTTP/1.0 400 Bad Request');
        } else {
            print_error('invalidop', 'block_accessibility');
        }
        exit();

        break;
}

$USER->fontsize = $new; // If we've just increased or decreased, save the new size to the session

if (accessibility_is_ajax()) {
    // If we're responding to AJAX, send back the new font size to change to
    header('Content-Type: application/json');
    $output = new stdClass;
    $output->fontsize = $USER->fontsize;
    $output->defaultsize = accessibility_getsize($USER->defaultfontsize);
    echo(json_encode($output));
    exit();
} else {
    // Otherwise, redirect the user
    redirect($redirecturl);
}
