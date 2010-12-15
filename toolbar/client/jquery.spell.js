/*! Copyright (c) 2008 Brandon Aaron (http://brandonaaron.net)
 * Dual licensed under the MIT (http://www.opensource.org/licenses/mit-license.php) 
 * and GPL (http://www.opensource.org/licenses/gpl-license.php) licenses.
 *
 * Version 0.2-pre
 *
 *
 * Heavily modified by Seb Skuse (scs@ecs.soton.ac.uk) to use Greasemonkey XHR requests, as well as custom XHR to get around limitations in other browsers.
 */
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
(function(jQuery){

/**
 * Creates an instance of a SpellChecker for each matched element.
 * The SpellChecker has several configurable options.
 *  - lang: the 2 letter language code, defaults to en for english
 *  - events: a space separated string of events to use, default is 'keypress blur paste'
 *  - autocheck: number of milliseconds to check spelling after a key event, default is 750.
 *  - url: url of the spellcheck service on your server, default is spellcheck.php
 *  - ignorecaps: 1 to ignore words with all caps, 0 to check them
 *  - ignoredigits: 1 to ignore digits, 0 to check them
 */
jQuery.sb_spellVersion = '3.5'; 
 
jQuery.fn.spellcheck = function(options) {
	return this.each(function() {
		var $this = jQuery(this);
		if ( !$this.is('[type=password]') && !$this.data('spellchecker') )
			jQuery(this).data('spellchecker', new jQuery.SpellChecker(this, options));
	});
};

jQuery.fn.removeSpellCheck = function(){
	return this.each(function() {
		jQuery(this).unbind(this.options.events);
		jQuery(this).removeData('spellchecker');
	});
}

jQuery.fn.rteSpellCheck = function(content, rteptr, options){
	return this.each(function(){
		var $this = jQuery(this);
		var checker = new jQuery.SpellChecker(this, options);
		checker.checkRTESpelling( content, rteptr, options.RTEType );
	});
}

/**
 * Forces a spell check on an element that has an instance of SpellChecker.
 */
jQuery.fn.checkspelling = function() {
	return this.each(function() {
		var spellchecker = $this.data('spellchecker');
		spellchecker && spellchecker.checkSpelling();
	});
};


jQuery.SpellChecker = function(element, options) {
	this.$element = jQuery(element);
	this.options = jQuery.extend({
		lang: 'en',
		autocheck: 750,
		events: 'keypress blur paste',
		url: 'spellcheck.php',
		useXHRMethod: 'GM-XHR',
		ignorecaps: 1,
		ignoredigits: 1,
		isRTE: false,
		RTEType: ''
	}, options);
	this.bindEvents();
};

jQuery.SpellChecker.prototype = {
	bindEvents: function() {
		if ( !this.options.events ) return;
		var self = this, timeout;
		this.$element.bind(this.options.events, function(event) {
			if ( /^key[press|up|down]/.test(event.type) ) {
				if ( timeout ) clearTimeout(timeout);
				timeout = setTimeout(function() { self.checkSpelling(); }, self.options.autocheck);
			} else
				self.checkSpelling(); 
		});
		// set recievedData here?
	},
	
	checkRTESpelling: function(input, rteptr, type){
		this.options.isRTE = true;
		this.origText = input;
		this.rteptr = rteptr;
		this.RTEType = type;
		
		var prevText = input.replace(/<.*?>/ig, '');
		this.text = input.replace(/<.*?>/ig, '');
		var self = this, timeout;
		
		// What XHR method are we using for this?
		if(this.options.useXHRMethod == "GM-XHR"){
			window.recievedData = "";
			GM_xmlhttpRequest({ method: "GET",
				url: settings.baseURL + "spell/spellcheck.php?lang=en&ignoredigits=1&ignorecaps=1&text=" + encodeURIComponent(this.text), 
				onload: self.writeData,
				onreadystatechange: self.checkStatus });
			// This is to check that we've recieved the data. GM times out otherwise, so we have to use a timer to keep it alive.
			this.ajaxInterval = setInterval(function(){
				self.checkDataResult();
			}, 100);
		} else {
			window.attachJS( settings.baseURL + 'xmlhttp/remote.php?rt=spell&id=' + Math.floor(Math.random() * 5001) + '&lang=en&ignoredigits=1&ignorecaps=1&text=' + encodeURIComponent(this.text), 'CS-XHR' );
			//alert("XS-XHR/GM-XHR not supported. Switching to " + XHRMethod);
			this.ajaxInterval = setInterval(function(){
				self.checkCSXHRResponse();
			}, 100);
		}
	},
	
	checkSpelling: function() {
		this.options.isRTE = false;
		var prevText = this.text, text = this.$element.val(), self = this;
		if ( prevText === text ) return;
		this.text = this.$element.val();
		
		// What XHR method are we using for this?
		if(this.options.useXHRMethod == "GM-XHR"){
			window.recievedData = "";
			GM_xmlhttpRequest({ method: "GET",
				url: settings.baseURL + "spell/spellcheck.php?lang=en&ignoredigits=1&ignorecaps=1&text=" + encodeURIComponent(this.text), 
				onload: self.writeData,
				onreadystatechange: self.checkStatus });
			// This is to check that we've recieved the data. GM times out otherwise, so we have to use a timer to keep it alive.
			this.ajaxInterval = setInterval(function(){
				self.checkDataResult();
			}, 100);
		} else {
			window.attachJS( settings.baseURL + 'xmlhttp/remote.php?rt=spell&id=' + Math.floor(Math.random() * 5001) + '&lang=en&ignoredigits=1&ignorecaps=1&text=' + encodeURIComponent(this.text), 'CS-XHR' );
			//alert("XS-XHR/GM-XHR not supported. Switching to " + XHRMethod);
			this.ajaxInterval = setInterval(function(){
				self.checkCSXHRResponse();
			}, 100);
		}
    
	},
	
	writeData: function(response){
		// Write the data out to a variable, as response is going to disapear!
		self.recievedData = response.responseText;
		//console.log("writing data:" + self.recievedData);
	},
	
	checkDataResult: function(){
		// Do we have data yet? If so, lets clear the interval and parse the results!
		
		if( window.recievedData != "" ){
			clearInterval( this.ajaxInterval );
			
			//console.log(window.recievedData);
			this.parseResults( window.recievedData );
			
			// Reset the storage variable for the xmlhttprequest'd data
			window.recievedData = "";
		}
	},
	
	checkCSXHRResponse: function(){
		// Do we have data yet? If so, lets clear the interval and parse the results!
		if( (typeof CSresponseObject) != "undefined" ){
			clearInterval( this.ajaxInterval );
			//console.log("Recieved: " + self.recievedData);
			
			// Copy the response object to a local object.
			var RO = CSresponseObject;
			
			// Remove the response JS.
			jQuery('#CS-XHR').remove();

			this.parseResults( RO.data );
		}		
	},
	
	parseResults: function(results) {
		var self = this;
		this.results = [];
		jQuery(results).find('c').each(function() {
			var $this = jQuery(this),
				offset = $this.attr('o'),
				length = $this.attr('l');
			self.results.push({
				word: self.text.substr(offset, length),
				suggestions: $this.text().split(/\s/)
			});
		});
		this.displayResults();
	},

	
	displayResults: function() {
		jQuery('#spellcheckresults').remove();
		if ( !this.results.length ) return;
		var $container = jQuery('<div id="spellcheckresults"></div>').appendTo('body'),
			dl = [], self = this, offset = this.$element.offset(), height = this.$element[0].offsetHeight, i, k;
		for ( i=0; i<this.results.length; i++ ) {
			var result = this.results[i], suggestions = result.suggestions;
			dl.push('<dl><dt>'+result.word+'</dt>');
			for ( k=0; k<suggestions.length; k++ )
				dl.push('<dd>'+suggestions[k]+'</dd>');
			dl.push('<dd class="ignore">ignore</dd></dl>');
		}
		
		$container.append(dl.join('')).find('dd').bind('click', function(event) {
			var $this = jQuery(this), $parent = $this.parent();
			if ( !$this.is('.ignore') ){
				if($this.isRTE == false || (typeof $this.isRTE) == 'undefined'){
					self.$element.val( self.$element.val().replace( $parent.find('dt').text(), $this.text() ) );
				} else {
					var tmpData = self.origText;
					self.origText = tmpData.replace( $parent.find('dt').text(), $this.text() );
					// Set the new content back to the RTE.
					if(self.RTEType == 'tMCE'){
						self.rteptr.setContent(self.origText);
					} else if(self.RTEType == 'CKE'){
						self.rteptr.setData(self.origText);
					}
				}
			}
			$parent.remove();
			if ( jQuery('#spellcheckresults').is(':empty') )
				jQuery('#spellcheckresults').remove();
			this.blur();
		}).end().css({ top: offset.top + height, left: offset.left });
	}
	
};

})(jQuery);