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

header('Content-Type: text/plain');
// First, check the session to see if the user's overridden the default/saved setting
$options = $DB->get_record('block_accessibility', array('userid' => $USER->id));

if (!empty($USER->fontsize)) {
    echo $USER->fontsize;
} else if (!empty($options->fontsize)) {
    echo $options->fontsize;
} else {
    echo -1;
}
echo ",";
if (!empty($USER->colourscheme)) {
    echo $USER->colourscheme;
} else if (!empty($options->colourscheme)) {
    echo $options->colourscheme;
} else {
    echo -1;
}
