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
 * @package   blocks-accessibility                                      (3)
 * @copyright 2009 Mark Johnson                                        (4)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later (5)
 */

require_once('../../config.php');
require_once($CFG->dirroot.'/blocks/accessibility/lib.php');

header('Cache-Control: no-cache');
if(!isset($USER->defaultfontsize)) {

    ob_start(); // Buffer the output so we're not really creating a stylesheet.
    style_sheet_setup(time(), 0, 'standard'); // Generate the stylesheets for the base and current themes
    style_sheet_setup(time(), 0, $CFG->theme);
    $styles = ob_get_contents(); // Get the generated sheets from the buffer
    ob_end_clean();
    $size = array();
    if(preg_match('/body\s?\{[^\}]*font[^:]*:([\d]{1,3})(px|%)[^\}]*}/', $styles, $size) > 0){
        // If there's a value in the stylesheet for the font size in pixels or percent
        $defaultsize = $size[1]; // use it
    } else {
        $defaultsize = 100; // Otherwise, use 100 as a sensible default
    }

    if($size[2] == '%' || $defaultsize == 100) {
        $USER->defaultfontsize = get_size($defaultsize);
    } else {
        $USER->defaultfontsize = $defaultsize;
    }

}

$op = required_param('op', PARAM_TEXT);

if (!accessibility_is_ajax()) {
    $redirect = required_param('redirect', PARAM_TEXT);
}

if(!isset($USER->fontsize)) {
    // If the user hasn't already changed the size, we need to find a default so we know where we're increasing/decreasing from
    if($userstyle = get_record('accessibility', 'userid', $USER->id)){
        // First, check the database to see if they've got a setting saved
        $current = $userstyle->fontsize;
    } else {
        // If not, use the default size from the theme.
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
        if($current == 26) {
            // If we're at the upper limit, don't increase any further.
            $new = accessibility_getsize($current);
        } else {
            // Otherwise, increase
            $new = accessibility_getsize($current+1);
        }
        break;
    case 'dec':
        if($current == 10) {
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
            echo('{oldfontsize: '.accessibility_getsize($current).', fontsize: '.accessibility_getsize($USER->defaultfontsize).'}');
            exit();
        } else {
            // Otherwise, redirect the user
            redirect($CFG->wwwroot.'/blocks/accessibility/database.php?op=reset&size=true&userid='.$USER->id.'&amp;redirect='.$redirect);
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
    echo('{fontsize: '.$USER->fontsize.', defaultsize: '.accessibility_getsize($USER->defaultfontsize).'}');
    exit();
} else {
    // Otherwise, redirect the user
    redirect($CFG->wwwroot.$redirect);
}

?>
