M.block_accessibility = {

    stylesheet: '',

    defaultsize: 100,

    colours: new Array(),

    styles: new Array(),

    numberOfColours: 3,

    watch: null,

    debug: false,

    init: function(Y, autoload_atbar) {
        this.log('Accessibility block Debug mode active');
        this.Y = Y;
        style2 = '*:not([class*="mce"]):not([id*="mce"]):not([id*="editor"]) {background-color: #ffc !important;}; forumpost .topic {background-image: none !important;} *:not([class*="mce"]):not([id*="mce"]):not([id*="editor"]){background-image: none !important;}';
        style3 = '*:not([class*="mce"]):not([id*="mce"]):not([id*="editor"]) {background-color: #9cf !important;} forumpost .topic {background-image: none !important;} *:not([class*="mce"]):not([id*="mce"]):not([id*="editor"]) {background-image: none !important;}';
        style4 = '*:not(a):not([class*="mce"]):not([id*="mce"]):not([id*="editor"]) {color: #ffff00 !important;} *:not([class*="mce"]):not([id*="mce"]):not([id*="editor"]) {background-color: #000 !important;} *:not([class*="mce"]):not([id*="mce"]):not([id*="editor"]) {background-image: none !important;} #content a, .tabrow0 span {color: #ff0 !important;} .tabrow0 span:hover {text-decoration: underline;} .block_accessibility .outer {border-color:#fff !important;} a {color: #f00 !important;}';
        this.styles.push(style2);
        this.styles.push(style3);
        this.styles.push(style4);
        this.stylesheet = Y.StyleSheet();
        for(var i=0; i<this.numberOfColours; i++) {
            this.colours[i] = Y.StyleSheet(this.styles[i]);
            this.colours[i].disable();
        }
        this.log('Initial size: '+Y.one('body').getStyle('fontSize'));
        var defaultsize = Y.one('body').getStyle('fontSize');
        if (defaultsize.substr(-2) == 'px') {
            this.defaultsize = defaultsize.substr(0, defaultsize.length-2);
        } else if (defaultsize.substr(-1) == '%') {
            this.defaultsize = defaultsize.substr(0, defaultsize.length-1);
        }

	//get colour scheme and font size
	xmlhttp = new XMLHttpRequest();
	xmlhttp.open("GET",M.cfg.wwwroot+"/blocks/accessibility/userstyles.php",false);
	xmlhttp.send(null);
	var fileContent = xmlhttp.responseText.split(",");

	//apply size
	var fontSize = parseInt(fileContent[0]);
	if(fontSize != -1) {
	    Y.one('#page').setStyle('fontSize', fontSize+'%');
	}

	//apply color
	var index = parseInt(fileContent[1]);
	if(index != -1 && index-2>=0) {
	    this.colours[index-2].enable();
	}

        // Attach the click handler
        Y.all('#block_accessibility_textresize a').on('click', function(e) {
            if (!e.target.hasClass('disabled')) {
                // If it is, and the button's not disabled, pass it's id to the changesize function
                M.block_accessibility.changesize(e.target);
            }
        });

        Y.all('#block_accessibility_changecolour a').on('click', function(e) {
            if (!e.target.hasClass('disabled')) {
                // If it is, and the button's not disabled, pass it's id to the changesize function
                M.block_accessibility.changecolour(e.target);
            }
        });

        Y.all('#block_accessibility_changecolour a').on('click', function(e) {
            if (!e.target.hasClass('disabled')) {
                // If it is, and the button's not disabled, pass it's id to the changesize function
                M.block_accessibility.changecolour(e.target);
            }
        });

        Y.one('#atbar_auto').on('click', function(e) {
            if (e.target.get('checked')) {
                M.block_accessibility.atbar_autoload('on');
            } else {
                M.block_accessibility.atbar_autoload('off');
            }
        });

        // Remove href attributes from anchors
        Y.all('#accessibility_controls a').each(function(node){
            node.removeAttribute('href');
        });

        // Create Bookmarklet-style link using code from ATbar site
        // http://access.ecs.soton.ac.uk/StudyBar/versions
        var launchbutton = Y.one('#block_accessibility_launchtoolbar');
        launchbutton.on('click', function() {
            M.block_accessibility.load_atbar();
            // Hide block buttons until ATbar is closed
            Y.one('#block_accessibility_textresize').setStyle('display', 'none');
            Y.one('#block_accessibility_changecolour').setStyle('display', 'none');
            M.block_accessibility.watch_atbar_for_close();
        });

        if (autoload_atbar) {
            M.block_accessibility.load_atbar();
            // Hide block buttons until ATbar is closed
            Y.one('#block_accessibility_textresize').setStyle('display', 'none');
            Y.one('#block_accessibility_changecolour').setStyle('display', 'none');
            // Wait 1 second to give the bar a chance to load
            setTimeout("M.block_accessibility.watch_atbar_for_close();", 1000);
        }

    },


    /**
     *  Code from ATbar bookmarklet to load bar into page
     */
    load_atbar: function() {
        d=document;
        jf=d.createElement('script');
        jf.src='https://core.atbar.org/atbar/en/latest/atbar.min.js';
        jf.type='text/javascript';
        jf.id='ToolBar';
        d.getElementsByTagName('head')[0].appendChild(jf);
    },

    /**
     * Displays the specified message in the block's footer
     *
     * @param {String} msg the message to display
     */
    show_message: function(msg) {
        this.log('Message set to '+msg);
        this.Y.one('#block_accessibility_message').setContent(msg);
    },

    /**
     * Calls the database script on the server to save the current setting to
     * the database. Displays a message on success, or an error on failure.
     *
     * @requires accessibility_show_message
     * @requires webroot
     *
     */
    savesize: function() {
        this.Y.io(M.cfg.wwwroot+'/blocks/accessibility/database.php', {
            data: 'op=save&size=true&scheme=true',
            method: 'get',
            on: {
                success: function(id, o) {
                    M.block_accessibility.show_message(M.util.get_string('saved', 'block_accessibility'));
                    setTimeout("M.block_accessibility.show_message('')", 5000);
                },
                failure: function(id, o) {
                    alert(M.util.get_string('jsnosave', 'block_accessibility')+' '+o.status+' '+o.statusText);
                }
            }
        });
    },

    /**
     * Calls the database script on the server to clear the current size setting from
     * the database. Displays a message on success, or an error on failure. 404 doesn't
     * count as a failure, as this just means there's no setting to be cleared
     *
     * @requires show_message()
     *
     */
    resetsize: function() {
        this.Y.io(M.cfg.wwwroot+'/blocks/accessibility/database.php', {
            data: 'op=reset&size=true',
            method: 'get',
            on: {
                success: function(id, o) {
                    M.block_accessibility.show_message(M.util.get_string('reset', 'block_accessibility'));
                    setTimeout("M.block_accessibility.show_message('')", 5000);
                },
                failure: function(id, o) {
                    if (o.status != '404') {
                        alert(M.util.get_string('jsnosizereset', 'block_accessibility')+' '+o.status+' '+o.statusText);
                    }
                }
           }
        });
    },

    /**
     * Calls the database script on the server to clear the current colour scheme setting from
     * the database. Displays a message on success, or an error on failure. 404 doesn't
     * count as a failure, as this just means there's no setting to be cleared
     *
     * @requires show_message()
     *
     */
     resetscheme: function() {
        this.Y.io(M.cfg.wwwroot+'/blocks/accessibility/database.php', {
            data: 'op=reset&scheme=true',
            method: 'get',
            on: {
                success: function(id, o) {
                    M.block_accessibility.show_message(M.util.get_string('reset', 'block_accessibility'));
                    setTimeout("M.block_accessibility.show_message('')", 5000);
                },
                failure: function(id, o) {
                    if (o.status != '404') {
                        alert(M.util.get_string('jsnocolourreset', 'block_accessibility')+': '+o.status+' '+o.statusText);
                    }
                }
           }
        });
    },

    /**
     * Enables or disables the buttons as specified
     *
     * @requires webroot
     *
     * @param {String} id the ID of the button to enable/disable
     * @param {String} op the operation we're doing, either 'on' or 'off'.
     *
     */
    toggle_textsizer: function(id, op) {
        var button = this.Y.one('#block_accessibility_'+id);
        if (op == 'on') {
            if (button.hasClass('disabled')) {
                this.log('Enabling '+button);
                button.removeClass('disabled');
            }
        } else if (op == 'off') {
            if(!button.hasClass('disabled')) {
                this.log('Disabling '+button);
                button.addClass('disabled');
            }
        }
    },

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
     * @param {Node} button the button that was pushed
     *
     */
    changesize: function(button) {
        Y = this.Y;

        switch (button.get('id')) {
            case "block_accessibility_inc":
                this.log('Increasing size from '+this.defaultsize);
                Y.io(M.cfg.wwwroot+'/blocks/accessibility/changesize.php', {
                    data: 'op=inc&cur='+this.defaultsize,
                    method: 'get',
                    on: {
                        success: function(id, o) {
                            // If we get a successful response from the server,
                            // Parse the JSON string
                            style = Y.JSON.parse(o.responseText);
                            // Set the new fontsize
                            M.block_accessibility.log('Increasing size to '+style.fontsize);
                            Y.one('#page').setStyle('fontSize', style.fontsize+'%');
                            // disable the per-user stylesheet so our style isn't overridden
                            if (M.block_accessibility.stylesheet !== undefined) {
                                M.block_accessibility.stylesheet.unset('#page');
                            }
                            // Disable/enable buttons as necessary
                            if(style.fontsize == style.defaultsize) {
                                M.block_accessibility.toggle_textsizer('reset', 'off');
                            } else {
                                M.block_accessibility.toggle_textsizer('reset', 'on');
                            }
                            if (style.fontsize == 197) {
                                M.block_accessibility.toggle_textsizer('inc', 'off');
                            } else if (style.fontsize == 85) {
                                M.block_accessibility.toggle_textsizer('dec', 'on');
                            }
                            M.block_accessibility.toggle_textsizer('save', 'on');
                        },
                        failure: function(o) {
                            alert(M.util.get_string('jsnosize', 'block_accessibility')+': '+o.status+' '+o.statusText);
                        }
                   }
                });
                break;
            case "block_accessibility_dec":
                this.log('Decreasing size from '+this.defaultsize);
                Y.io(M.cfg.wwwroot+'/blocks/accessibility/changesize.php', {
                    data: 'op=dec&cur='+this.defaultsize,
                    method: 'get',
                    on: {
                        success: function(id, o) {
                            // If we get a successful response from the server,
                            // Parse the JSON string
                            style = Y.JSON.parse(o.responseText);
                            // Set the new fontsize
                            M.block_accessibility.log('Decreasing size to '+style.fontsize);
                            Y.one('#page').setStyle('fontSize', style.fontsize+'%');
                            // disable the per-user stylesheet so our style isn't overridden
                            if (M.block_accessibility.stylesheet !== undefined) {
                                M.block_accessibility.stylesheet.unset('#page');
                            }
                            // Disable/enable buttons as necessary
                            if(style.fontsize == style.defaultsize) {
                                M.block_accessibility.toggle_textsizer('reset', 'off');
                            } else {
                                M.block_accessibility.toggle_textsizer('reset', 'on');
                            }
                            if (style.fontsize == 77) {
                                M.block_accessibility.toggle_textsizer('dec', 'off');
                            } else if (style.fontsize == 189) {
                                M.block_accessibility.toggle_textsizer('inc', 'on');
                            }
                            M.block_accessibility.toggle_textsizer('save', 'on');
                        },
                        failure: function(id, o) {
                            alert(M.util.get_string('jsnosize', 'block_accessibility')+': '+o.status+' '+o.statusText);
                        }
                   }
                });
                break;
            case "block_accessibility_reset":
                this.log('Resetting size from '+this.defaultsize);
                Y.io(M.cfg.wwwroot+'/blocks/accessibility/changesize.php', {
                    data: 'op=reset&cur='+this.defaultsize,
                    method: 'get',
                    on: {
                        success: function(id, o) {
                            // If we get a successful response from the server,
                            // Parse the JSON string
                            style = Y.JSON.parse(o.responseText);
                            // Set the new fontsize
                            M.block_accessibility.log('Resetting size to '+style.fontsize);
                            Y.one('#page').setStyle('fontSize', style.fontsize+'%');
                            // disable the per-user stylesheet so our style isn't overridden
                            if (M.block_accessibility.stylesheet !== undefined) {
                                M.block_accessibility.stylesheet.unset('#page');
                            }
                            // Disable/enable buttons as necessary
                            M.block_accessibility.toggle_textsizer('reset', 'off');
                            if(style.oldfontsize == 77) {
                                M.block_accessibility.toggle_textsizer('dec', 'on');
                            } else if (style.oldfontsize == 197){
                                M.block_accessibility.toggle_textsizer('inc', 'on');
                            }
                            M.block_accessibility.toggle_textsizer('save', 'off');
                            M.block_accessibility.resetsize();
                        },
                        failure: function(id, o) {
                            alert(M.util.get_string('jsnosize', 'block_accessibility')+': '+o.status+' '+o.statusText);
                        }
                   }
                });
                break;
            case "block_accessibility_save":
                this.log('Saving Size');
                M.block_accessibility.savesize();
                break;
        }
    },

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
     * @param {String} button - the button that was clicked.
     *
     */

    changecolour: function(button) {
        Y = this.Y;
        scheme = button.get('id').substring(26);
        Y.io(M.cfg.wwwroot+'/blocks/accessibility/changecolour.php', {
            data: 'scheme='+scheme,
            method: 'get',
            on: {
                success: function (id, o) {
                    if (M.block_accessibility.stylesheet !== undefined) {
                        M.block_accessibility.stylesheet.unset('*:not(a):not([class*="mce"]):not([id*="mce"]):not([id*="editor"])');
                        M.block_accessibility.stylesheet.unset('*:not([class*="mce"]):not([id*="mce"]):not([id*="editor"])');
			M.block_accessibility.stylesheet.unset('forumpost .topic');
                        M.block_accessibility.stylesheet.unset('#content a, .tabrow0 span');
                        M.block_accessibility.stylesheet.unset('.tabrow0 span:hover');
                        M.block_accessibility.stylesheet.unset('.block_accessibility .outer');
                    }

		    var enabled = (scheme-2)>=0;
		    for(var i = 0; i<M.block_accessibility.numberOfColours; i++) {
			M.block_accessibility.colours[i].disable();
			if(scheme-2 == i) {
			    M.block_accessibility.colours[i].enable();
			}
			if(enabled) {
			    M.block_accessibility.toggle_textsizer('save','on');
			} else {
			    M.block_accessibility.resetscheme();
			    M.block_accessibility.toggle_textsizer('save','off');
			}
		    }
		},

                failure: function(id, o) {
                    alert(get_string('jsnocolour', 'block_accessibility')+': '+o.status+' '+o.statusText);
                }
            }
        });
    },

    atbar_autoload: function(op) {
        if (op == 'on') {
            this.Y.io(M.cfg.wwwroot+'/blocks/accessibility/database.php', {
                data: 'op=save&atbar=true',
                method: 'get',
                on: {
                    success: function(id, o) {
                        M.block_accessibility.show_message(M.util.get_string('saved', 'block_accessibility'));
                        setTimeout("M.block_accessibility.show_message('')", 5000);
                    },
                    failure: function(id, o) {
                        if (o.status != '404') {
                            alert(M.util.get_string('jsnosave', 'block_accessibility')+': '+o.status+' '+o.statusText);
                        }
                    }
               }
            });
        } else if (op == 'off') {
            this.Y.io(M.cfg.wwwroot+'/blocks/accessibility/database.php', {
                data: 'op=reset&atbar=true',
                method: 'get',
                on: {
                    success: function(id, o) {
                        M.block_accessibility.show_message(M.util.get_string('reset', 'block_accessibility'));
                        setTimeout("M.block_accessibility.show_message('')", 5000);
                    },
                    failure: function(id, o) {
                        if (o.status != '404') {
                            alert(M.util.get_string('jsnoreset', 'block_accessibility')+': '+o.status+' '+o.statusText);
                        }
                    }
               }
            });
        }
    },

    watch_atbar_for_close: function() {
        Y = this.Y;
        this.watch = setInterval(function() {
            if (AtKit.isRendered()) {
                Y.one('#block_accessibility_textresize').setStyle('display', 'block');
                Y.one('#block_accessibility_changecolour').setStyle('display', 'block');
                clearInterval(M.block_accessibility.watch);
            }
        }, 1000);
    },

    log: function(data) {
        if (this.debug) {
            console.log(data);
        }
    }
}
