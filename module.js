M.block_accessibility = {
	/*
		Note: As of 29.04.2014. no duplicate CSS declaration in both module.js and userstyles.php
		CSS is now declared and generated based on user settings in a single point - userstyles.php
		module.js script updates user settings and fetch updated stylesheet over AJAX for each action
	*/

	ATBAR_SRC: 'https://core.atbar.org/atbar/en/latest/atbar.min.js',

	// font sizes in %, this is defined in changesize.php as well
	DEFAULT_FONTSIZE: 100,
	MAX_FONTSIZE: 197,
	MIN_FONTSIZE: 77,

	// only in JS-mode, because .getStyle('fontSize') will return computed style in px
	DAFAULT_PX_FONTSIZE: 13,
	MAX_PX_FONTSIZE: 26,
	MIN_PX_FONTSIZE: 10,

	stylesheet: '',

	sheetnode: '',

	instance_id: '',

	defaultsize: null,

	watch: null,

	debug: false,


	init: function(Y, autoload_atbar, instance_id) {
		this.log('Accessibility block Debug mode active');
		this.Y = Y;
		this.instance_id = instance_id;
		
		this.sheetnode = Y.one('link[href="'+M.cfg.wwwroot+
			'/blocks/accessibility/userstyles.php?instance_id='+instance_id+'"]');
		this.stylesheet = Y.StyleSheet(this.sheetnode);

		// Set default font size
		this.log('Initial size: '+Y.one('body').getStyle('fontSize'));
		this.defaultsize = M.block_accessibility.get_current_fontsize('body');

		// Attach the click handler
		Y.all('#block_accessibility_textresize a').on('click', function(e) {
			if (!e.target.hasClass('disabled')) {
				// If it is, and the button's not disabled, pass it's id to the changesize function
				M.block_accessibility.changesize(e.target);
			}
		});

		Y.all('#block_accessibility_changecolour a').on('click', function(e) {
			if (!e.target.hasClass('disabled')) {
				// If it is, and the button's not disabled, pass it's id to the changecolour function
				M.block_accessibility.changecolour(e.target);
			}
		});

		// Remove href attributes from anchors
		Y.all('#accessibility_controls a').each(function(node){
			node.removeAttribute('href');
		});

		// ATBar might be disabled in block's config
		if(Y.one('#atbar_auto') !== null){
			// checkbox for setting 'always' chackbox
		   Y.one('#atbar_auto').on('click', function(e) {
				if (e.target.get('checked')) {
					M.block_accessibility.atbar_autoload('on');
				} else {
					M.block_accessibility.atbar_autoload('off');
				}
			});
			
			// Create Bookmarklet-style link using code from ATbar site
			// http://access.ecs.soton.ac.uk/StudyBar/versions
			Y.one('#block_accessibility_launchtoolbar').on('click', function() {
				M.block_accessibility.load_atbar();

				// Do we really need it?
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
		}

		// assign loader icon events
		Y.on('io:start', M.block_accessibility.show_loading);
		Y.on('io:complete', M.block_accessibility.hide_loading);
				

	},


	/**
	 *  Code from ATbar bookmarklet to load bar into page
	 */
	load_atbar: function() {
		var jf = document.createElement('script');
		jf.src = M.block_accessibility.ATBAR_SRC;
		jf.type = 'text/javascript';
		jf.id = 'ToolBar';
		document.getElementsByTagName('head')[0].appendChild(jf);
	},

	/**
	 * Displays the specified message in the block's footer
	 *
	 * @param {String} msg the message to display
	 */
	show_message: function(msg) {
		this.log('Message set to '+msg);
		this.Y.one('#block_accessibility_message').setContent(msg);

		// make message disappear after some time
		if(msg) setTimeout("M.block_accessibility.show_message('')", 5000);
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
					//setTimeout("M.block_accessibility.show_message('')", 5000);
				},
				failure: function(id, o) {
					alert(M.util.get_string('jsnosave', 'block_accessibility')+' '+o.status+' '+o.statusText);
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
					data: 'op=inc&cur='+this.defaultsize, // we need to find a default so we know where we're increasing/decreasing from, otherwise PHP will assume 100%
					method: 'get',
					on: {
						success: function(id, o) {

							// now that we updated user setting to the server, load updated stylesheet
							M.block_accessibility.reload_stylesheet();  
							var new_fontsize =  M.block_accessibility.get_current_fontsize('#page');
							M.block_accessibility.log('Increasing size to '+new_fontsize);

							// Disable/enable buttons as necessary
							var min_fontsize = M.block_accessibility.MIN_PX_FONTSIZE;
							var max_fontsize = M.block_accessibility.MAX_PX_FONTSIZE;
							if(new_fontsize == M.block_accessibility.defaultsize) {
								M.block_accessibility.toggle_textsizer('reset', 'off');
							} else {
								M.block_accessibility.toggle_textsizer('reset', 'on');
							}
							if (new_fontsize >= max_fontsize) {
								M.block_accessibility.toggle_textsizer('inc', 'off');
							} else if (new_fontsize <= min_fontsize) {
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

							// now that we updated user setting to the server, load updated stylesheet
							M.block_accessibility.reload_stylesheet();
							var new_fontsize =  M.block_accessibility.get_current_fontsize('#page');
							M.block_accessibility.log('Decreasing size to '+new_fontsize);

							
							// Disable/enable buttons as necessary
							var min_fontsize = M.block_accessibility.MIN_PX_FONTSIZE;
							var max_fontsize = M.block_accessibility.MAX_PX_FONTSIZE;
							if(new_fontsize == M.block_accessibility.defaultsize) {
								M.block_accessibility.toggle_textsizer('reset', 'off');
							} else {
								M.block_accessibility.toggle_textsizer('reset', 'on');
							}
							if (new_fontsize <= min_fontsize) {
								M.block_accessibility.toggle_textsizer('dec', 'off');
							} else if (new_fontsize == max_fontsize) {
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

							// now that we updated user setting to the server, load updated stylesheet
							M.block_accessibility.reload_stylesheet();
							var new_fontsize =  M.block_accessibility.get_current_fontsize('#page');
							M.block_accessibility.log('Resetting size to '+new_fontsize);
  
							// Disable/enable buttons as necessary
							var min_fontsize = M.block_accessibility.MIN_PX_FONTSIZE;
							var max_fontsize = M.block_accessibility.MAX_PX_FONTSIZE;
							M.block_accessibility.toggle_textsizer('reset', 'off');
							if(new_fontsize <= min_fontsize) {
								M.block_accessibility.toggle_textsizer('dec', 'on');
							} else if (new_fontsize >= max_fontsize){
								M.block_accessibility.toggle_textsizer('inc', 'on');
							}
							M.block_accessibility.toggle_textsizer('save', 'off');
							//M.block_accessibility.resetsize();
							
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
					M.block_accessibility.reload_stylesheet(); 
					if(scheme == 1) M.block_accessibility.toggle_textsizer('save', 'off'); // reset
					else M.block_accessibility.toggle_textsizer('save', 'on');
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
						//setTimeout("M.block_accessibility.show_message('')", 5000);
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
						//setTimeout("M.block_accessibility.show_message('')", 5000);
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
	},

	/**
	 * Stylesheet is generated by userstyles.php based on user settings (e.g. font size)
	 * After settings are changed, the script should download and include updated stylesheet
	 *
	 */
	reload_stylesheet: function(){
		var cache_prevention_salt = new Date().getTime();

		/*
			Why wouldn't we just set the href attribute insted of creating a new node? Because before the new stylesheet is loaded and while old one is deleted, the page loose all the styles and all the elements get unstyled for a some time
		*/
		var oldStylesheet = M.block_accessibility.sheetnode;
		var newStylesheet = oldStylesheet.cloneNode(true);
		newStylesheet.set(
			'href', M.cfg.wwwroot+
			'/blocks/accessibility/userstyles.php?instance_id='+
			M.block_accessibility.instance_id+
			'&v='+cache_prevention_salt
		); 
		this.Y.one('head').append(newStylesheet);
		newStylesheet.getDOMNode().onload = function(){ oldStylesheet.remove() };
		M.block_accessibility.sheetnode = newStylesheet;


		//alert(M.block_accessibility.sheetnode + ' '+ clone);

		

	},

	/**
	 * For improved user experience, only in JS-mode, we can set current font size as default font size
	 * We would initially put 100%, but it doesn't have to be true for all themes
	 * Also font-size value can be in % or in px, there is mapping defined in lib.php in the block
	 * The function needs to return percentage fontsize value as integer
	 *
	 * @param {String} root element to check font-size declaration (e.g. body or #page)
	 */
	get_current_fontsize: function(root_element){
		var currentsize = M.block_accessibility.DEFAULT_FONTSIZE;
		//var defaultsize = Y.one(root_element).getStyle('fontSize');
		var defaultsize = Y.one(root_element).getComputedStyle('fontSize');
		if (defaultsize.substr(-2) == 'px') {
			currentsize = defaultsize.substr(0, defaultsize.length-2);
		} else if (defaultsize.substr(-1) == '%') {
			currentsize = defaultsize.substr(0, defaultsize.length-1);
		}
		return currentsize;
	},

	show_loading: function(){
		Y.one('#loader-icon').setStyle('display', 'block');
		Y.one('#accessibility_controls').setStyle('opacity', '0.2');
	},
	hide_loading: function(){
		Y.one('#loader-icon').setStyle('display', 'none');
		Y.one('#accessibility_controls').setStyle('opacity', '1');
	}
}
