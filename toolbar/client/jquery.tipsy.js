 /*!
 * StudyBar
 *
 * Copyright (c) 2009. University of Southampton
 * http://access.ecs.soton.ac.uk/StudyBar/
 *
 * Licensed under the BSD Licence.
 * http://www.opensource.org/licenses/bsd-license.php
 *
 */
(function(jQuery) {

	jQuery.fn.tipsy = function(opts) {
		opts = jQuery.extend({animation: 'slide', openSpeed: 50, closeSpeed: 200, closeDelay: 500, gravity: 's', enabled: true}, opts || {});
		var tip = null;					// Stores the element of the tip
		var cancelHide = false;			// var title = null;
		var opening = false;			// Stores whether the tip is opening, to prevent broken sliding
		var timeouts = new Array();		// Stores tip-close timeouts so they can be cancelled later

		if(opts.enabled == true) {
			this.hover(function() {
				if(!opening) {
					opening = true;
					jQuery.data(this, 'cancel.tipsy', true);

					// Kill all outstanding timeouts then kill existing tips
					for (var i = timeouts.length - 1; i >= 0; i--) clearTimeout(timeouts[i]), delete timeouts[i];
					jQuery('.tipsy').each(function(index, tip) {
						removeTip(jQuery(tip));
					});

					var tip = jQuery.data(this, 'active.tipsy');
					if (!tip) {
						tip = jQuery('<div class="tipsy"><div class="tipsy-inner">' + breakText(jQuery(this).attr('title')) + '</div></div>');
						tip.css({position: 'absolute'});
						jQuery(this).removeAttr('title');
						jQuery.data(this, 'active.tipsy', tip);
					}

					// need to get offset or width WITHOUT padding
			   		var pos = jQuery.extend(jQuery(this).offset(), { height: jQuery(this).height(), width: jQuery(this).width() });

					// Renders the tip off-screen to take width measurements, then hides it
					tip.css({marginTop: '18px', top: '0', left: 0, display: 'block'}).appendTo(document.body);
					var actualWidth = tip[0].offsetWidth, actualHeight = tip[0].offsetHeight;
					tip.hide();

					// Corrects left-position for padding, margin and borders
					pos.left += parseInt(jQuery(this).css('padding-left').replace("px", ""));
					pos.left += parseInt(jQuery(this).css('margin-left').replace("px", ""));
					pos.left += parseInt(jQuery(this).css('border-left-width').replace("px", ""));

					// Per-gravity offset calculation stuff
					switch (opts.gravity.charAt(0)) {
						case 'n':
							tip.css({top: pos.top - actualHeight, left: pos.left + pos.width / 2 - actualWidth / 2}).addClass('tipsy-north');
							break;
						case 's':
							tip.css({top: pos.top + pos.height, left: pos.left + pos.width / 2 - actualWidth / 2}).addClass('tipsy-south');
							break;
						case 'e':
							tip.css({top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left + pos.width}).addClass('tipsy-east');
							break;
						case 'w':
							tip.css({top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left - actualWidth}).addClass('tipsy-west');
							break;
					}

					// Kill all running animation on the tip and set the 'end' state. Then show it.
					tip.stop(true, true);
					if (opts.animation == 'slide') {
						tip.animate({opacity: 'show', top: '-=15'}, opts.openSpeed, function() {
							opening = false;
						});
					} else if(opts.animation == 'fade') {
						tip.fadeIn(opts.openSpeed,function() {
							opening = false;
						});
					} else {
						tip.show();
						opening = false;
					}
				}
			}, function(w) {
				jQuery.data(this, 'cancel.tipsy', false);
				var self = this;
				timeouts.push(setTimeout(function() {
					if (jQuery.data(this, 'cancel.tipsy')) return;
					var tip = jQuery.data(self, 'active.tipsy');
					removeTip(tip);
				}, opts.closeDelay));
			});
		} else {
			jQuery(this).removeAttr('title');
			this.unbind("mouseover");
			this.unbind("mouseout");
		}

		function breakText(text){
			var l = 27;
			if(text.length < l) return text;
			var o = "";
			
			x=1;
			for(i=0; i <= text.length; i = i+l){
				o += text.substring(i, l*x);
				if(l*x < text.length) o += "<br />";
				x++;
			}
			return o;
		}

		function removeTip(tip) {
			tip.stop(true, true);
			if (opts.animation == 'slide') {
				tip.animate({opacity: 'hide', top: '+=15'}, opts.closeSpeed, function() {
					tip.remove();
				});
			} else if(opts.animation == 'fade') {
				tip.fadeIn(opts.closeSpeed,function() {
					tip.remove();
				});
			} else {
				tip.remove();
			}
		}
	};
})(jQuery);