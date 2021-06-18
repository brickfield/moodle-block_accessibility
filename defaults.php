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
 * Declares the default settings of the page.
 *
 * @package   block_accessibility
 * @copyright Copyright &copy; 2009 Taunton's College
 * @author    Mark Johnson
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
$defaults = array(
    // The fg1 and bg1 would be reset/default colour - do not define it.
        'bg2' => '#FFFFCC',
        'fg2' => '', // Default theme colours will be unchanged.
        'bg3' => '#99CCFF',
        'fg3' => '',
        'bg4' => '#000000',
        'fg4' => '#FFFF00',
);
