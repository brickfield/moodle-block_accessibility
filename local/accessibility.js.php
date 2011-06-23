<?php
require_once('../../../config.php');
header('Content-Type: text/javascript');
?>
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
 * Defines the functions for dynamically resizing text with AJAX       (1)
 *
 * This file defines the 6 Javascript functions we need to resize the
 * text without reloading the page. All the calculations are done server
 * -side, this just gets the necessary bits via AJAX. PHP is used to
 * generate this file as it requires some lang strings.                (2)
 *
 * @package   blocks-accessibility                                      (3)
 * @copyright Copyright &copy; 2009 Taunton's College                   (4)
 * @author Mark Johnson                                                    (5)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later (6)
 */



/**
 * Displays the specified message in the block's footer
 *
 * @param {String} msg the message to display
 */
function accessibility_show_message(msg) {
    document.getElementById('accessibility_message').innerHTML = msg;
}

/**
 * Calls the database script on the server to save the current setting to
 * the database. Displays a message on success, or an error on failure.
 *
 * @requires accessibility_show_message
 * @requires webroot
 *
 */
function accessibility_savesize() {
    YAHOO.util.Connect.asyncRequest(
        'get',
        webroot+'/blocks/accessibility/database.php?op=save&size=true&scheme=true',
        {
            success: function(o) {
            accessibility_show_message('<?php print_string('saved', 'block_accessibility'); ?>');
            setTimeout("accessibility_show_message('')", 5000);
            },
            failure: function(o) {
                alert('<?php print_string('jsnosave', 'block_accessibility'); ?> '+o.status+' '+o.statusText);
            }
       }
   );
}

/**
 * Calls the database script on the server to clear the current size setting from
 * the database. Displays a message on success, or an error on failure. 404 doesn't
 * count as a failure, as this just means there's no setting to be cleared
 *
 * @requires accessibility_show_message
 * @requires webroot
 *
 */
function accessibility_resetsize() {
    YAHOO.util.Connect.asyncRequest(
        'get',
        webroot+'/blocks/accessibility/database.php?op=reset&size=true',
        {
            success: function(o) {
                accessibility_show_message('<?php print_string('reset', 'block_accessibility'); ?>');
                setTimeout("accessibility_show_message('')", 5000);
            },
            failure: function(o) {
                if (o.status != '404') {
                    alert('<?php print_string('jsnosizereset', 'block_accessibility'); ?> '+o.status+' '+o.statusText);
                }
            }
       }
   );
}

/**
 * Calls the database script on the server to clear the current colour scheme setting from
 * the database. Displays a message on success, or an error on failure. 404 doesn't
 * count as a failure, as this just means there's no setting to be cleared
 *
 * @requires accessibility_show_message
 * @requires webroot
 *
 */
function accessibility_resetscheme() {
    YAHOO.util.Connect.asyncRequest(
        'get',
        webroot+'/blocks/accessibility/database.php?op=reset&scheme=true',
        {
            success: function(o) {
                accessibility_show_message('<?php print_string('reset', 'block_accessibility'); ?>');
                setTimeout("accessibility_show_message('')", 5000);
            },
            failure: function(o) {
                if (o.status != '404') {
                    alert('<?php print_string('jsnocolourreset', 'block_accessibility'); ?>: '+o.status+' '+o.statusText);
                }
            }
       }
   );
}

/**
 * Enables or disables the buttons as specified
 *
 * @requires webroot
 *
 * @param {String} button the ID of the button to enable/disable
 * @param {String} op the operation we're doing, either 'on' or 'off'.
 *
 */
function accessibility_toggle_textsizer(button, op) {
    if(op == 'on') {
        if(YAHOO.util.Dom.hasClass(button, 'disabled')) {
            YAHOO.util.Dom.removeClass(button, 'disabled');
        }
        if(button == 'save') {
            YAHOO.util.Dom.get('saveicon').src = webroot+'/blocks/accessibility/pix/document-save.png';
        }
    } else if (op == 'off') {
        if(!YAHOO.util.Dom.hasClass(button, 'disabled')) {
            YAHOO.util.Dom.addClass(button, 'disabled');
        }
        if(button == 'save') {
            YAHOO.util.Dom.get('saveicon').src = webroot+'/blocks/accessibility/pix/document-save-grey.png';
        }
    }
}

/**
 * This handles clicks from the buttons. If increasing, decreasing or
 * resetting size, it calls changesize.php via AJAX and sets the text
 * size to the number returned from the server. If saving the size, it
 * calls accessibility_savesize.
 * Also enables/disables buttons as required when sizes are changed.
 *
 * @requires accessibility_toggle_textsizer
 * @requires accessibility_savesize
 * @requires accessibility_resetsize
 * @requires stylesheet
 * @requires webroot
 *
 * @param {String} op the operation we're doing, either 'inc' or 'dec', 'reset' or 'save'.
 *
 */
function accessibility_changesize(op) {
    switch (op) {
        case "inc":
            YAHOO.util.Connect.asyncRequest(
                'get',
                webroot+'/blocks/accessibility/changesize.php?op=inc',
                {
                    success: function(o) {
                        // If we get a successful response from the server,
                        // Parse the JSON string
                        style = eval('('+o.responseText+')');
                        // Set the new fontsize
                        YAHOO.util.Dom.setStyle(document.body, 'font-size', style.fontsize+'%');
                        // disable the per-user stylesheet so our style isn't overridden
                        if (stylesheet !== undefined) {
                            if (fontrule != null) {
                                if (stylesheet.rules !== undefined && stylesheet.rules[fontrule] !== undefined) {
                                    stylesheet.removeRule(fontrule);
                                    fontrule = null;
                                } else if (stylesheet.cssRules !== undefined && stylesheet.cssRules[fontrule] !== undefined) {
                                    stylesheet.deleteRule(fontrule);
                                    fontrule = null;
                                }
                            }
                        }
                        // Disable/enable buttons as necessary
                        if(style.fontsize == style.defaultsize) {
                            accessibility_toggle_textsizer('reset', 'off');
                        } else {
                            accessibility_toggle_textsizer('reset', 'on');
                        }
                        if (style.fontsize == 197) {
                            accessibility_toggle_textsizer('inc', 'off');
                        } else if (style.fontsize == 85) {
                            accessibility_toggle_textsizer('dec', 'on');
                        }
                        accessibility_toggle_textsizer('save', 'on');
                    },
                    failure: function(o) {
                        alert('<?php print_string('jsnosize', 'block_accessibility'); ?>: '+o.status+' '+o.statusText);
                    }
               }
            );
            break;
        case "dec":
            YAHOO.util.Connect.asyncRequest(
                'get',
                webroot+'/blocks/accessibility/changesize.php?op=dec',
                {
                    success: function(o) {
                        // If we get a successful response from the server,
                        // Parse the JSON string
                        style = eval('('+o.responseText+')');
                        // Set the new fontsize
                        YAHOO.util.Dom.setStyle(document.body, 'font-size', style.fontsize+'%');
                        // disable the per-user stylesheet so our style isn't overridden
                        if (stylesheet !== undefined) {
                            if (fontrule != null) {
                                if (stylesheet.rules !== undefined && stylesheet.rules[fontrule] !== undefined) {
                                    stylesheet.removeRule(fontrule);
                                    fontrule = null;
                                } else if (stylesheet.cssRules !== undefined && stylesheet.cssRules[fontrule] !== undefined) {
                                    stylesheet.deleteRule(fontrule);
                                    fontrule = null;
                                }
                            }
                        }
                        // Disable/enable buttons as necessary
                        if(style.fontsize == style.defaultsize) {
                            accessibility_toggle_textsizer('reset', 'off');
                        } else {
                            accessibility_toggle_textsizer('reset', 'on');
                        }
                        if (style.fontsize == 77) {
                            accessibility_toggle_textsizer('dec', 'off');
                        } else if (style.fontsize == 189) {
                            accessibility_toggle_textsizer('inc', 'on');
                        }
                        accessibility_toggle_textsizer('save', 'on');
                    },
                    failure: function(o) {
                        alert('<?php print_string('jsnosize', 'block_accessibility'); ?>: '+o.status+' '+o.statusText);
                    }
               }
            );
            break;
        case "reset":
            YAHOO.util.Connect.asyncRequest(
                'get',
                webroot+'/blocks/accessibility/changesize.php?op=reset',
                {
                    success: function(o) {
                        // If we get a successful response from the server,
                        // Parse the JSON string
                        style = eval('('+o.responseText+')');
                        // Set the new fontsize
                        YAHOO.util.Dom.setStyle(document.body, 'font-size', style.fontsize+'%');
                        // disable the per-user stylesheet so our style isn't overridden
                        if (stylesheet !== undefined) {
                            if (fontrule != null) {
                                if (stylesheet.rules !== undefined && stylesheet.rules[fontrule] !== undefined) {
                                    stylesheet.removeRule(fontrule);
                                    fontrule = null;
                                } else if (stylesheet.cssRules !== undefined && stylesheet.cssRules[fontrule] !== undefined) {
                                    stylesheet.deleteRule(fontrule);
                                    fontrule = null;
                                }
                            }
                        }
                        // Disable/enable buttons as necessary
                        accessibility_toggle_textsizer('reset', 'off');
                        if(style.oldfontsize == 77) {
                            accessibility_toggle_textsizer('dec', 'on');
                        } else if (style.oldfontsize == 197){
                            accessibility_toggle_textsizer('inc', 'on');
                        }
                        accessibility_toggle_textsizer('save', 'off');
                        accessibility_resetsize();
                    },
                    failure: function(o) {
                        alert('<?php print_string('jsnosize', 'block_accessibility'); ?>: '+o.status+' '+o.statusText);
                    }
               }
            );
            break;
        case "save":
            accessibility_savesize();
            break;
    }
}

/**
 * This handles clicks from the colour scheme buttons.
 * We start by getting the scheme number from the theme button's ID.
 * We then get the elements that need dynamically re-styling via their
 * CSS selectors and loop through the arrays to style them appropriately.
 *
 * @requires accessibility_toggle_textsizer
 * @requires accessibility_resetscheme
 * @requires stylesheet
 * @requires webroot
 *
 * @param {String} scheme - the colour scheme we're changing to, 1-4.
 *
 */

function accessibility_changecolour(scheme) {
    scheme = scheme.substring(6);
    YAHOO.util.Connect.asyncRequest(
        'get',
        webroot+'/blocks/accessibility/changecolour.php?scheme='+scheme,
        {
            success: function (o) {
	            if (stylesheet !== undefined) {
	                if (colourrules.length > 0) {
	                    for (i=0; i<colourrules.length; i++) {
	                        if (stylesheet.rules !== undefined && stylesheet.rules[i] !== undefined) {
	                            stylesheet.removeRule(colourrules[i]);
	                            colourrules.splice(i);
	                        } else if (stylesheet.cssRules !== undefined && stylesheet.cssRules[i] !== undefined) {
	                            stylesheet.deleteRule(colourrules[i]);
	                            colourrules.splice(i);
	                        }
	                    }
	                }
	            }
                elements = new Array();
                elements = YAHOO.util.Selector.query('*');
                a = YAHOO.util.Selector.query('a');

                switch (scheme) {
                    case '1':
                        YAHOO.util.Dom.setStyle(elements, 'background-color', '');
                        YAHOO.util.Dom.setStyle(elements, 'color', '');
                        YAHOO.util.Dom.setStyle(elements, 'background-image', '');
                        YAHOO.util.Dom.setStyle(a, 'color', '');
                        accessibility_resetscheme();
                        accessibility_toggle_textsizer('save', 'off');
                        break;
                    case '2':
                    YAHOO.util.Dom.setStyle(elements, 'background-color', '#ffffcc');
                        YAHOO.util.Dom.setStyle(elements, 'color', '');
                        YAHOO.util.Dom.setStyle(elements, 'background-image', 'none');
                        YAHOO.util.Dom.setStyle(a, 'color', '');
                        accessibility_toggle_textsizer('save', 'on');
                        break;
                    case '3':
                    YAHOO.util.Dom.setStyle(elements, 'background-color', '#99ccff');
                        YAHOO.util.Dom.setStyle(elements, 'color', '');
                        YAHOO.util.Dom.setStyle(elements, 'background-image', 'none');
                        YAHOO.util.Dom.setStyle(a, 'color', '');
                        accessibility_toggle_textsizer('save', 'on');
                        break;
                    case '4':
                    YAHOO.util.Dom.setStyle(elements, 'background-color', '#000000');
                        YAHOO.util.Dom.setStyle(elements, 'color', '#ffff00');
                        YAHOO.util.Dom.setStyle(elements, 'background-image', 'none');
                        YAHOO.util.Dom.setStyle(a, 'color', '#ff0000');
                        accessibility_toggle_textsizer('save', 'on');
                        break;
                }
                /*
                 * Local bits to keep main menu looking OK.
                 */
                menuimg = YAHOO.util.Dom.getElementsByClassName('mainmenu_button');
                menuimg.push(YAHOO.util.Dom.get('mainmenu'));
                menu = YAHOO.util.Selector.query('li.mainmenu_button, li.mainmenu_button *');
                YAHOO.util.Dom.setStyle(menuimg, 'background-image', '');
                YAHOO.util.Dom.setStyle(menuimg, 'background-color', '');
                YAHOO.util.Dom.setStyle(menu, 'background-color', '');
                YAHOO.util.Dom.setStyle(menu, 'color', '');

            },

            failure: function(o) {
                alert('<?php print_string('jsnocolour', 'block_accessibility'); ?>: '+o.status+' '+o.statusText);
            }
        }
     );
}

/**
 * Handles click events inside the textsizer div. All clicks are handled by this function,
 * then if they're inside an element we're worried about (i.e. the buttons) it passes that
 * element's id to accessibilty_changesize.
 *
 * @requires accessibility_changesize
 *
 * @param e the click event that fired the function
 *
 */
function accessibility_sizeclickhandler(e) {
    var elTarget = YAHOO.util.Event.getTarget(e); // Get the DOM node that the click came from
    while (elTarget.id != "textresize") { // If we're still inside the textresize div
        if(YAHOO.util.Dom.hasClass(elTarget.id, 'outer')) { // Check the clicks from an element we're after
            if (!YAHOO.util.Dom.hasClass(elTarget.id, 'disabled')) {
                // If it is, and the button's not disabled, pass it's id to the changesize function
                accessibility_changesize(elTarget.id);
            }
            break;
        } else {
            // Otherwise, look at the node's parent to see if we're after that one.
            elTarget = elTarget.parentNode
        }
    }
}

/**
 * Handles click events inside the colourchange div. All clicks are handled by this function,
 * then if they're inside an element we're worried about (i.e. the buttons) it passes that
 * element's id to accessibilty_changecolour.
 *
 * @requires accessibility_changecolour
 *
 * @param e the click event that fired the function
 *
 */
function accessibility_schemeclickhandler(e) {
    var elTarget = YAHOO.util.Event.getTarget(e); // Get the DOM node that the click came from
    while (elTarget.id != "colourchange") { // If we're still inside the changecolour div
        if(YAHOO.util.Dom.hasClass(elTarget.id, 'outer')) { // Check the clicks from an element we're after
            if (!YAHOO.util.Dom.hasClass(elTarget.id, 'disabled')) {
                // If it is, and the button's not disabled, pass it's id to the changecolour function
                accessibility_changecolour(elTarget.id);
            }
            break;
        } else {
            // Otherwise, look at the node's parent to see if we're after that one.
            elTarget = elTarget.parentNode
        }
    }
}

/**
 * The initialisation function for the block's javascript - run with body.onload
 * This gets a reference to the per-user stylesheet so we can disable it later,
 * attaches the clickhandler function to the textsizer div, removes the hrefs from
 * the <a> tags, disables the reset button and enables the save button.
 *
 * @requires accessibility_clickhandler
 * @requires accessibility_toggle_textsizer
 * @requires stylesheet
 * @requires webroot
 *
 */
function accessibility_init() {
    for (i=0; i<document.styleSheets.length; i++) {
        // Find the reference to the per-user stylesheet
        if(document.styleSheets[i].title == 'access_stylesheet') {
            stylesheet = document.styleSheets[i];
            if(stylesheet.cssRules !== undefined) {
                for(i=0; i<stylesheet.cssRules.length; i++) {
                    if(stylesheet.cssRules[i].selectorText == 'body') {
                        fontrule = i;
                    } else if (stylesheet.cssRules[i].selectorText == '*'){
                        colourrules.push(i);
                    }
                }
            } else {
                for(i=0; i<stylesheet.rules.length; i++) {
                    if(stylesheet.rules[i].selectorText == 'body') {
                        fontrule = i;
                    } else if (stylesheet.rules[i].selectorText == '*'){
                        colourrules.push(i);
                    }
                }
            }
            break;
        }
    }
    // Attach the click handler
    YAHOO.util.Event.on("textresize", "click", accessibility_sizeclickhandler);
    YAHOO.util.Event.on("colourchange", "click", accessibility_schemeclickhandler);
    // Remove href attributes from anchors
    elements = YAHOO.util.Dom.getElementsByClassName('outer', 'a');
    for(i=0; i<elements.length; i++) {
        elements[i].removeAttribute('href');
    }
}