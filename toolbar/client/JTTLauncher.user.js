/*!
* TweakBar
*
* Copyright (c) 2009. University of Southampton
* http://access.ecs.soton.ac.uk/StudyBar/
*
* Licensed under the BSD Licence.
* http://www.opensource.org/licenses/bsd-license.php
*
*/
// --------------------------------------------------------------------
//
// This is a Greasemonkey user script.
//
// To install, you need Greasemonkey: http://greasemonkey.mozdev.org/
// Then restart Firefox and revisit this script.
// Under Tools, there will be a new menu item to "Install User Script".
// Accept the default configuration and install.
//
// To uninstall, go to Tools/Manage User Scripts,
// select "JISC TechDis Toolbar", and click Uninstall.
//
// --------------------------------------------------------------------
//
// ==UserScript==
// @name          JISC Techdis Toolbar
// @namespace     http://access.ecs.soton.ac.uk/ToolBar/
// @description   JISC Techdis Toolbar cross-platform, cross-browser Accessibility toolbar
// @include       *
// @match         http://*/*
// @require       http://code.jquery.com/jquery-latest.js
// ==/UserScript==

javascriptFile = document.createElement("script");
javascriptFile.src = "http://access.ecs.soton.ac.uk/StudyBar/channels/toolbar-stable/JTToolbar.user.js";
javascriptFile.type = "text/javascript";
javascriptFile.id = "JISCTechdisToolbar";
document.getElementsByTagName('head')[0].appendChild(javascriptFile);