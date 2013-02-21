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
 * Sets per-user styles                                                (1)
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

require_once('../../config.php');
require_once($CFG->dirroot.'/blocks/accessibility/lib.php');
if (!isloggedin()) {
    die();
}

header('Content-Type: text/css');
// First, check the session to see if the user's overridden the default/saved setting
$options = $DB->get_record('block_accessibility', array('userid' => $USER->id));

if (!empty($USER->fontsize)) {

    $fontsize = $USER->fontsize;

} else if (!empty($options->fontsize)) {
    $fontsize = $options->fontsize;
}
if (!empty($USER->colourscheme)) {

    $colourscheme = $USER->colourscheme;

} else if (!empty($options->colourscheme)) {

     $colourscheme = $options->colourscheme;

}

if (!empty($fontsize) || !empty($colourscheme)) {
    // Echo out CSS for the body element. Use !important to override any other external
    // stylesheets.
    if (!empty($fontsize)) {
        echo '#page {font-size: '.$fontsize.'% !important;}';
    }
    if (!empty($colourscheme)) {
        switch ($colourscheme) {
            case 2:
                echo '* {background-color: #ffc !important;};
                    forumpost .topic {background-image: none !important;}
                    * {background-image: none !important;}';
                break;

            case 3:
                echo '* {background-color: #9cf !important;}
                    forumpost .topic {background-image: none !important;}
                    * {background-image: none !important;}';
                break;

            case 4:
                echo '* {color: #ffff00 !important;}
                    * {background-color: #000 !important;}
                    * {background-image: none !important;}
                    #content a, .tabrow0 span {color: #ff0 !important;}
                    .tabrow0 span:hover {text-decoration: underline;}
                    .block_accessibility .outer {border-color:#fff !important;}';
                break;

        }
    }
}
