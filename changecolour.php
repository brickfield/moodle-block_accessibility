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
 * Sets the session variable for custom colour schemes                  (1)
 *
 * This page accepts the required colour scheme as an argument, and
 * sets a session variable accordingly. If the colour scheme is 1 (the
 * theme default) the variable is unset.
 * If the page is being requested via AJAX, we just return HTTP 200, or
 * 400 if the parameter was invalid. If requesting normally, we redirect
 * to reset the saved setting, or to the page we came from as required. (2)
 *
 * @package   block_accessibility                                      (3)
 * @copyright Copyright &copy; 2009 Taunton's College                   (4)
 * @author  Mark Johnson                                               (5)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later (6)
 * @param int scheme - The number of the colour scheme, 1-4             (7)
 */

// INITIALIZATION
// =========================================================
require_once('../../config.php');
require_once($CFG->dirroot.'/blocks/accessibility/lib.php');
require_login();

$scheme = required_param('scheme', PARAM_INT);

switch($scheme) {
    case 1: 
        // Clear the scheme stored in the session
        unset($USER->colourscheme);

        // Clear user records in database
        $urlparams = array(
            'op' => 'reset',
            'scheme' => true,
            'userid' => $USER->id
        );
        if(!accessibility_is_ajax()){
            $redirect = required_param('redirect', PARAM_TEXT);
            $urlparams['redirect'] = $redirect; 
        }
        $redirecturl = new moodle_url('/blocks/accessibility/database.php', $urlparams);
        redirect($redirecturl);
        break;

    case 2:
    case 3:
    case 4:
        $USER->colourscheme = $scheme;
        break;

    default:
        header("HTTP/1.0 400 Bad Request");
        break;
}

if (accessibility_is_ajax()) {
    // it would be good idea to include userstyles.php here as HTTP response
    // this would save one extra HTTP request from module.js
} else {
    $redirect = required_param('redirect', PARAM_TEXT);
    $redirecturl = new moodle_url($redirect);
    redirect($redirecturl);
}
