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
 * Sets styles for the block                                           (1)
 *
 * This file is mainly static CSS used to display the block. It also
 * includes the YUI fonts CSS to make sure that the resizing will all
 * work as expected.                                                   (2)
 *
 * @package   blocks-accessibility                                      (3)
 * @copyright Copyright &copy; 2009 Taunton's College                   (4)
 * @author   Mark Johnson                                                (5)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later (6)
 */


require ('../../config.php');
require_once($CFG->dirroot.'/lib/yui/fonts/fonts-min.css');

?>

#textresize,  #colourchange {
	margin-left:auto;
    margin-right:auto;
    width: 151px;
}

#textresize .outer,  #colourchange .outer {
    width: 32px;
    height: 32px;
    border: 1px solid black;
	color: black;
    list-style: none;
    display: table;
    float: left;
    cursor: pointer;
    text-align: center;
    overflow: hidden;
    position: relative;
}

#textresize .outer *,  #colourchange .outer * {
    background-color: #fcfcfc !important;
    color: #000 !important;
}

/* Verticaly centering text achieved using Vertical Center Solution From
http://www.jakpsatweb.cz/css/css-vertical-center-solution.html
Still needs some work in IE 6/7 */

#textresize .outer[id], #colourchange .outer[id] {
    display: table;
    position: static;
    list-style: none;
}

#textresize div.middle, #colourchange div.middle {
    position: absolute;
}

#textresize div.middle[class], #colourchange div.middle[class] {
    display: table-cell;
    vertical-align: middle;
    position: static;
}

#textresize div.inner, #colourchange div.inner{
	position: relative;
    top: 25%;
    text-align:center;
}

#textresize .outer *:hover {
    background-color: #9cf !important;
    text-decoration:none;
}

#textresize div.inner, #textresize div.inner img {
	background-color: inherit !important;
}


#textresize .outer.disabled {
    color: grey;
    cursor: pointer;
}

#textresize .outer.disabled:hover {
    background-color: #fcfcfc !important;
}


#textresize .outer#dec {
    font-size: 12px;
}

#textresize .outer#reset {
    font-size: 16px;
}

#textresize .outer#inc {
    font-size: 20px;

}
#textresize .right, #colourchange .right {
	margin-left: 5px;
}

#colourchange .row {
    margin-top: 5px;
}

#colourchange .outer {
	font-size: 16px;
}

#colourchange .outer:hover {
    font-weight: bold;
}

#colourchange .outer:hover, #textrezise .outer:hover {
	text-decoration:none;
}

#colourchange #colour2 * {
    background-color: #FFFFCC !important;
}

#colourchange #colour3 * {
    background-color: #99CCFF !important;
}

#colourchange #colour4 * {
    background-color: #000000 !important;
    color: #ffff00 !important;
}

?>