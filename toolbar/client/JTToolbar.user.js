/*!
 * ATbar
 *
 * Copyright (c) 2009. University of Southampton
 * http://access.ecs.soton.ac.uk/ToolBar/
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
// select "ATbar", and click Uninstall.
//
// --------------------------------------------------------------------
//
// ==UserScript==
// @name          ATbar
// @namespace     http://access.ecs.soton.ac.uk/ToolBar/
// @description   ATbar cross-platform, cross-browser Accessibility toolbar
// @include       *
// @require       http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js
// @require       http://access.ecs.soton.ac.uk/ToolBar/channels/toolbar-stable/jquery.spell.js
// @require       http://access.ecs.soton.ac.uk/ToolBar/channels/toolbar-stable/jquery.tipsy.js
// @require       http://access.ecs.soton.ac.uk/ToolBar/channels/toolbar-stable/jquery.facebox.js
// @require       http://access.ecs.soton.ac.uk/ToolBar/channels/toolbar-stable/button.class.js
// ==/UserScript==

var versionString = "1.2.36";
var buildStatus = "";
var relChannel = "toolbar-stable";

var settings = {
				stylesheetURL: "presentation/style.css",
				baseURL: "http://access.ecs.soton.ac.uk/seb/StudyBar/",
				clientRoot: "http://access.ecs.soton.ac.uk/ToolBar/",
				ttsSplitChunkSize: 700,
				invoked: false
				};


var toolbarItems = {
		ftw: { id: 'ftwTB', ico: 'FTW.png', act: 'fixTheWeb()', tip: 'Create a fix the web report about this page (opens in a popup window)', clickEnabled: true },
		resizeUp: { id: 'resizeUp', ico: 'font_increase.png', act: 'resizeText(1);', tip: 'Increase text size', clickEnabled: true },
		resizeDown: { id: 'resizeDown', ico: 'font_decrease.png', act: 'resizeText(-1)', tip: 'Decrease text size', clickEnabled: true },
		fontSettings: { id: 'fontSettings', ico: 'font.png', act: 'fontSettingsDialog()', tip: 'Font settings', clickEnabled: true,
				dialogs: {
					main: "<h2>Page font settings</h2><label for=\"sbfontface\">Font Face:</label> <select id=\"sbfontface\"><option value=\"sitespecific\">--Site Specific--</option><option value=\"arial\">Arial</option><option value=\"courier\">Courier</option><option value=\"cursive\">Cursive</option><option value=\"fantasy\">Fantasy</option><option value=\"georgia\">Georgia</option><option value=\"helvetica\">Helvetica</option><option value=\"impact\">Impact</option><option value=\"monaco\">Monaco</option><option value=\"monospace\">Monospace</option><option value=\"sans-serif\">Sans-Serif</option><option value=\"tahoma\">Tahoma</option><option value=\"times new roman\">Times New Roman</option><option value=\"trebuchet ms\">Trebuchet MS</option><option value=\"verdant\">Verdana</option></select><br /><br /> <label for=\"sblinespacing\">Line Spacing:</label> <input type=\"text\" name=\"sblinespacing\" id=\"sblinespacing\" maxlength=\"3\" size=\"3\" value=\"100\">%<br /><br /><div class=\"sbarDialogButton\"><a id=\"sbfontfaceapply\" href=\"#s-b-c\"> <img src=\"" + settings.baseURL + "presentation/images/dialog/arrow.png\" /> Apply</a></div>"
				}
		},
		spell: { id: 'spell', ico: 'spell-off.png', act: 'spellCheckPage()', tip: 'Start spellchecker', clickEnabled: true, checkerEnabled: false },
		dictionary: { id: 'dictionary', ico: 'book_open.png', act: 'getDictionaryRef()', tip: 'Dictionary', clickEnabled: true },
		TTS: { id: 'tts', ico: 'sound.png', act: 'ttsOptions()', tip: 'Text to Speech options', clickEnabled: true, positition: "", playingItem: "",
				dialogs: {
					options: "<h2>Text to Speech options</h2> What do you want to convert to speech? <div class=\"sbarDialogButton\"> <a id=\"sbStartTTS\" href=\"#s-b-c\"> <img src=\"" + settings.baseURL + "presentation/images/dialog/arrow.png\" /> Entire page</a></div> <div class=\"sbarDialogButton\"> <a id=\"sbStartTTSSelection\" href=\"#s-b-c\"> <img src=\"" + settings.baseURL + "presentation/images/dialog/arrow.png\" /> Selected text</a></div>",
					starting: "<h2>Text To Speech</h2> <center>Text to Speech conversion is taking place. <br /><img src='http://access.ecs.soton.ac.uk/seb/StudyBar/presentation/images/loadingbig.gif' /><br />Time remaining: <div id='sbttstimeremaining'>calculating</div><br />Please wait... </center>"
				},
				extendedButtons: {
					controlBox: "<div id=\"sbAudioControlBox\"> <div id=\"sb-btn-plpaus\" class=\"sb-btn\"><a title=\"Play / Pause\" id=\"sb-tts-plpaus\" href=\"#s-b-c\"><img id=\"sb-btnico-plpaus\" src=\"" + settings.baseURL + "presentation/images/control-pause.png\" border=\"0\" /></a></div> <div id=\"sb-btn-rwd\" class=\"sb-btn\"><a title=\"Rewind\" id=\"sb-tts-rwd\" href=\"#s-b-c\"><img id=\"sb-btnico-rwd\" src=\"" + settings.baseURL + "presentation/images/control-stop-180.png\" border=\"0\" /></a></div> <div id=\"sb-btn-stop\" class=\"sb-btn\"><a title=\"Stop & Close TTS\" id=\"sb-tts-stop\" href=\"#s-b-c\"><img id=\"sb-btnico-stop\" src=\"" + settings.baseURL + "presentation/images/control-stop-square.png\" border=\"0\" /></a></div> </div>"
				}
		},
		references: { id: 'references', ico: 'book_link.png', act: 'referencesDialog()', tip: 'References', clickEnabled: true,
				dialogs: {
					landingDialog: "<h2>References</h2> <p>You can use this function to find information on this page to make a reference.<br /><br />What type of material are you referencing?</p><select id=\"sbReferenceType\"> </select><br /><br /> <div class=\"sbarDialogButton\"><a id=\"sbScanReferences\" href=\"#s-b-c\"> <img src=\"" + settings.baseURL + "presentation/images/dialog/arrow.png\" /> Scan Page</a></div>",
					results: "<h2>Reference Scan Results</h2><p>Below are the results that we've found on this page.</p><br /> <table border=\"0\"><tr><td><b>Author:</b></td><td>{{author}}</td></tr><tr><td><b>Date:</b></td><td>{{date}}</td></tr><tr><td><b>Page Title:</b></td><td>{{ptitle}}</td></tr><tr><td><b>Name of Website:</b></td><td>{{wsname}}</td></tr><tr><td><b>Accessed:</b></td><td>{{accessed}}</td></tr><tr><td><b>URL:</b></td><td>{{url}}</td></tr></table>"
				}
		},
		CSS: { id: 'changecss', ico: 'palette.png', act: 'changeColours(0)', tip: 'Change Styles', clickEnabled: true, 
				dialogs: { 
					colourDialog: "<h2>Change colour settings</h2> <div class=\"sbarDialogButton\"> <a id=\"sbColourChange\" href=\"#s-b-c\"> <img src=\"" + settings.baseURL + "presentation/images/dialog/arrow.png\" /> Change Toolbar colour</a></div> <div class=\"sbarDialogButton\"><a id=\"sbChangeSiteColours\" href=\"#s-b-c\"> <img src=\"" + settings.baseURL + "presentation/images/dialog/arrow.png\" /> Change text and link colours</a></div> <div class=\"sbarDialogButton\"><a id=\"sbAttachCSSStyle\" href=\"#s-b-c\"> <img src=\"" + settings.baseURL + "presentation/images/dialog/arrow.png\" /> Change page style</a></div>",
					sbColourDialog: "<h2>Change ATbar colour</h2> <label for=\"sbbackgroundcolour\">Background Colour: </label><input type=\"text\" name=\"sbbackgroundcolour\" id=\"sbbackgroundcolour\"> <a id=\"sbSetColour\" href=\"#s-b-c\"><img src=\"" + settings.baseURL + "/presentation/images/accept.png \" /> Set</a> <br /> <div id=\"cp-container\"><a class=\"sb-cp-box\" href=\"#s-b-c\" onclick=\"document.getElementById('sbbackgroundcolour').value = 'black';\">Black</a> <a class=\"sb-cp-box\" href=\"#s-b-c\" onclick=\"document.getElementById('sbbackgroundcolour').value = 'white';\">White</a> <a class=\"sb-cp-box\" href=\"#s-b-c\" onclick=\"document.getElementById('sbbackgroundcolour').value = 'grey';\">Grey</a></div> <br /> <div class=\"sbarDialogButton\"><a id=\"sbRandomColour\" href=\"#s-b-c\"> <img src=\"" + settings.baseURL + "presentation/images/dialog/arrow.png\" /> Random</a></div> <div class=\"sbarDialogButton\"> <a id=\"sbColourReset\" href=\"#s-b-c\"> <img src=\"" + settings.baseURL + "presentation/images/dialog/arrow.png\" /> Reset to Default</a></div>",
					sbSiteColours: "<h2>Change text and link colours</h2> <label for=\"sbtextcolour\" style=\"display:block\">Text Colour: </label><select id=\"sbtextcolour\" name=\"sbtextcolour\"><option value=\"original\">--Original--</option><option value=\"B80028\">Red</option><option value=\"194E84\">Blue</option><option value=\"60BB22\">Green</option><option value=\"FDB813\">Yellow</option><option value=\"F17022\">Orange</option><option value=\"000000\">Black</option><option value=\"A8B1B8\">Grey</option><option value=\"FFFFFF\">White</option></select><br /><label for=\"sblinkcolour\" style=\"display:block\">Link Colour: </label><select id =\"sblinkcolour\"><option value=\"original\">--Original--</option><option value=\"B80028\">Red</option><option value=\"194E84\">Blue</option><option value=\"60BB22\">Green</option><option value=\"FDB813\">Yellow</option><option value=\"F17022\">Orange</option><option value=\"000000\">Black</option><option value=\"A8B1B8\">Grey</option><option value=\"FFFFFF\">White</option></select> <div class=\"sbarDialogButton\"><a id=\"applyPageColours\" href=\"#s-b-c\"> <img src=\"" + settings.baseURL + "presentation/images/dialog/arrow.png\" /> Apply</a></div>",
					sbAttachCSS: "<h2>Change page style</h2><div class=\"sbarDialogButton\"><a id=\"sbApplyCSS-wb\" href=\"#s-b-c\"> <img src=\"" + settings.baseURL + "presentation/images/dialog/arrow.png\" /> Black on White</a></div> <div class=\"sbarDialogButton\" href=\"#s-b-c\"><a id=\"sbApplyCSS-wbw\"> <img src=\"" + settings.baseURL + "presentation/images/dialog/arrow.png\" /> White on Black</a></div> <div class=\"sbarDialogButton\"><a id=\"sbApplyCSS-yb\" href=\"#s-b-c\"> <img src=\"" + settings.baseURL + "presentation/images/dialog/arrow.png\" /> Yellow on Black</a></div> <div class=\"sbarDialogButton\"><a id=\"sbApplyCSS-by\" href=\"#s-b-c\"> <img src=\"" + settings.baseURL + "presentation/images/dialog/arrow.png\" /> Black on Yellow</a></div> <div class=\"sbarDialogButton\"><a id=\"sbApplyCSS-gw\" href=\"#s-b-c\"> <img src=\"" + settings.baseURL + "presentation/images/dialog/arrow.png\" /> White on Grey</a></div>"
				} 
		},
		resetPage: { id: 'resetPage', ico: 'arrow-curve-180-left.png', act: 'resetPage()', tip: 'Reset page', styleClass: ' fright', clickEnabled: true },
		help : { id: 'help', ico: 'information.png', act: 'studybarHelp()', tip: 'Help / Information', styleClass: ' fright', clickEnabled: true }
	};

var sysDialog = {
	closeDialog: "<h2>ATbar is about to exit</h2> Are you sure you want to close ATbar? <div class=\"sbarDialogButton\"><a href=\"#s-b-c\" id=\"sbCloseAllSites\"> <img src=\"" + settings.baseURL + "presentation/images/dialog/arrow.png\" /> Yes</a></div>",
	aboutBox: "<h2>About ATbar</h2>Version " + versionString + " " + buildStatus + " (" + relChannel + " release channel)<br /><div id=\"SBversionLatest\" style=\"margin-top:5px;\"><img src=\"" + settings.baseURL + "presentation/images/clock.png\" align=\"left\" style=\"margin-right:5px;\"/> Checking for updates...</div><br /><p style=\"line-height:120%\">Created by <a href=\"http://ecs.soton.ac.uk/people/scs/\">Sebastian Skuse</a> and the <a href=\"http://access.ecs.soton.ac.uk/\">Accessibility Team</a>.<br />Learning Societies Lab, ECS<br /> &copy; University of Southampton 2009.<br /><br />Fugue Icons &copy; <a href=\"http://www.pinvoke.com/\">pinvoke</a> under Creative Commons licence, Dictionary &copy; <a href=\"http://en.wiktionary.org/\">Wiktionary</a> under Creative Commons licence.<br /><a href=\"http://famspam.com/facebox/\">Facebox</a> jQuery plugin &copy; Chris Wanstrath under MIT licence, Portions of the spelling engine &copy; <a href=\"http://brandonaaron.net\">Brandon Aaron</a> under MIT licence.</p>"

};

var XHRMethod;
var rteSpellTimer;
var SBaudioPlayer;

var head = document.getElementsByTagName('head')[0];

// <Name> loadStudyBar
// <Purpose> Main constructor. Injects the studybar HTML into the DOM of the current page and sets up variables that may be required.
// <ToDo> Nothing.

window.loadStudyBar = function(){
	if(settings.invoked == false){
		doc = document;
		
		// Set the method of XHR that we're going to use.
		setXHRMethod();
		
		// Create the div for StudyBar.
		bar = doc.createElement('div');
		// Set the ID of the toolbar.
		bar.id = "sbar";
		
		jQuery.noConflict();
		
		// Insert it as the first node in the body node.
		jQuery(bar).insertAfter("#sbarGhost");
		
		// Add logo to studybar.
		jQuery("<a id=\"sbarlogo\"><img src=\"http://access.ecs.soton.ac.uk/ToolBar/channels/ATBar/images/atbar.png\" align=\"left\" border=\"0\" title=\"About\" style=\"margin-top:10px; float:left !important;\" /></a>").appendTo('#sbar');
		
		jQuery("<img src='" + settings.clientRoot + "stat.php?channel=" + relChannel + "&version=" + versionString + "' />").appendTo("#sbar");		
		
		// Add items to the toolbar.
		populateBar();
		
		if( (typeof GM_setValue) != 'undefined' ) {
			// Set studybar to load next time we load a page.
			GM_setValue('autoload', true);
		}
		
		jQuery("#sbarlogo").bind("click", function(e){ 
			jQuery.facebox( sysDialog.aboutBox + "<div style=\"font-size:10px\">SpellCheck Client v" + jQuery.sb_spellVersion + ", fbox v" + jQuery.sb_faceboxVersion + "</div>" );
			
			if((typeof StudyBarNoSandbox) == 'undefined') {
				setTimeout(function(){ checkUpdate(); }, 500);
			} else {
				jQuery('#SBversionLatest').html("<img src=\"" + settings.baseURL + "presentation/images/tick-circle-frame.png\" align=\"left\" style=\"margin-right:5px\" /> You are running the latest version.<br />");
			}
		});
		
		if ( isIE6() ) { // IE6
			jQuery('#sbarGhost').css('height', '1px');
			jQuery('#sbarGhost').remove();
		}
		
		jQuery('#sbarGhost').html("&nbsp;");
		
		// Any sites that have compatibility issues, issue any fixes we know about.
		siteFixes();
	
		settings.invoked = true;
		
		// Bind shortcut
		
		jQuery(document).keyup(function (e) {
			if(e.which == 17) isCtrl=false;
		}).keydown(function (e) {
			if(e.which == 17) isCtrl=true;
			if(e.which == 49 && isCtrl == true) {
				jQuery('#sbar').find('.sb-btn:first').children('a').focus();
				return false;
			}
		});
		
	}
}


// <Name> populateBar
// <Purpose> Populate the bar with the different toolbar items defined in object at start.
// <ToDo> Nothing.

window.populateBar = function(){
	// Check to see if tipsy has loaded yet.
	if( (typeof jQuery.fn.tipsy) == 'function' && (typeof returnNewButton) == 'function'){
		jQuery('#sbarlogo img').tipsy({gravity: 's'});
		for ( var i in toolbarItems ){
			var item = returnNewButton( toolbarItems[i].id, toolbarItems[i].ico, toolbarItems[i].act, toolbarItems[i].tip, toolbarItems[i].styleClass, settings.baseURL );
		}
		returnNewButton( 'closeSBar', 'cross.png', 'unloadOptions()', 'Exit Toolbar', 'closeButton', settings.baseURL );	
	} else {
		setTimeout('populateBar()', 100);
	}
}

// <Name> identifyBrowser
// <Purpose> Identify the user's current browser, so we can modify how StudyBar behaves in other areas of the script

window.identifyBrowser = function(){
	if(navigator.appName == 'Microsoft Internet Explorer'){
		return "IE";
	} else {
		if (/Firefox/.test(navigator.userAgent)){
		 	return "FF";
		} else {
			return navigator.appName;
		}	
	}	
}

window.isIE6 = function(){
	return /MSIE 6/i.test(navigator.userAgent);
}

window.attachJS = function(url, id){
	javascriptFile = document.createElement("script");
	javascriptFile.src = url;
	javascriptFile.type = "text/javascript";
	javascriptFile.id = id;
	head.appendChild(javascriptFile);	
}


window.checkUpdate = function(){
	if(XHRMethod == "GM-XHR"){
		GM_xmlhttpRequest({ method: "GET",
				url: settings.clientRoot + "update.php?b=" + identifyBrowser() + "&ch=" + relChannel, 
				onload: updatecheckResult
			});	
	
	} else {
		var ua = navigator.userAgent.toLowerCase();
		if(identifyBrowser() == 'Safari' || identifyBrowser() == 'IE' || ua.indexOf( "safari" ) != -1 || ua.indexOf( "chrome" ) == -1) {	
		
			jQuery.getJSON(settings.baseURL + 'xmlhttp/remote.php?rt=update&b=' + identifyBrowser() + '&ch=' + relChannel + '&callback=?', function(data) {
    			updatecheckResult(data);
			});

		} else {
			jQuery('#SBversionLatest').html("<img src=\"" + settings.baseURL + "presentation/images/tick-circle-frame.png\" align=\"left\" style=\"margin-right:5px\" /> You are running the latest version.<br /> This browser will auto-update ATbar.");
		}
	}

}

window.fixTheWeb = function(){

	loc = window.location.toString();
	loc = loc.replace(window.location.hash.toString(), "");
	var url = "http://www.fixtheweb.net/frame/report?url=" + encodeURI(loc);
	
	var load = window.open(url,'','scrollbars=no,menubar=no,height=260,width=370,resizable=no,toolbar=no,location=no,status=no');
	if (window.focus) {load.focus()}
	
	var externalWindow = load.document;
	
	externalWindow.all.edit-field-report-url.focus();
}

window.updatecheckResult = function(response){
	if(XHRMethod == "GM-XHR"){
		var ro = eval("(" + response.responseText + ")");
	} else {
		var ro = response;
	}
	
	var serverVer = ro.ver;
	var thisVer = versionString;
	
	serverVer = serverVer.replace(/[.]/g, '');
	thisVer = thisVer.replace(/[.]/g, '');
	
	if(serverVer > thisVer){
		alert("ATbar Version " + ro.ver + " is available! You have " + versionString + ". You will now be prompted to install the new version.");
		window.location = ro.updateURL;
	} else {
		if(thisVer > serverVer){
			jQuery('#SBversionLatest').html("<img src=\"" + settings.baseURL + "presentation/images/exclamation-octagon.png\" align=\"left\" style=\"margin-right:5px;\" /> You are running a pre-release version of ATbar.<br /> Click <a href=\"" + ro.stableURL + "\">here</a> for the current stable release.");
		} else {
			var stableMatch = /.*?stable$/ig;
			
			if(stableMatch.test(relChannel) == false){
				jQuery('#SBversionLatest').html("<img src=\"" + settings.baseURL + "presentation/images/exclamation-octagon.png\" align=\"left\" style=\"margin-right:5px;\" /> You are running the latest version, but it is marked as " + relChannel + ".<br /> Click <a href=\"" + ro.stableURL + "\">here</a> for the current stable release.");
			} else {
				jQuery('#SBversionLatest').html("<img src=\"" + settings.baseURL + "presentation/images/tick-circle-frame.png\" align=\"left\" style=\"margin-right:5px;\" /> You are running the latest version.");
			}
		}
	}
}


window.siteFixes = function(){
	// We apply any site-specific fixes here. This is usually for sites that are using absolute positioning (usually for no real reason).
	if( /\.orkut\.com$/i.test(window.location.host) == true ) jQuery('#orkutFrame').css("margin-top", "40px");
	if( /\.ask\.com$/i.test(window.location.host) == true ) jQuery('#navbar_search').css('position', 'relative');
	if( /\.channel4\.com$/i.test(window.location.host) == true ){
		jQuery('#navigation').css('position', 'relative');
		jQuery('#c4ad-Top object:parent').css('top', '45px');
	}
	if( /access\.ecs\.soton\.ac\.uk/i.test(window.location.host) == true ){
		jQuery('.container, .heading').css('top', '258px');
	}
	
	if(/techdis\.ac\.uk/i.test(window.location.host) == true){
		jQuery('body > p:empty').remove();
		jQuery('.headerpadding > div').css('margin-top', '50px');
	}
	
	if(/efolio\.soton\.ac\.uk/i.test(window.location.host) == true || /ergo\.soton\.ac\.uk/i.test(window.location.host) == true){
		if(identifyBrowser() == "IE"){
			var val = parseInt( jQuery('body').css('background-position-y') );
		} else {
			var val = parseInt(jQuery('body').css('background-position').split(' ')[1]);
		}
		
		jQuery('body').css('background-position', '100% ' + (val+40) +  'px');
		jQuery('div#toppanel').css('top', '40px');
	}
}

/////////////////////////////////////////////////
// Functions called from within the bar below  //
/////////////////////////////////////////////////

window.attachCSS = function(url, id){
	stylesheet = document.createElement("link");
	stylesheet.href = url;
	stylesheet.rel = "stylesheet";
	stylesheet.type = "text/css";
	stylesheet.id = id;
	
	if(id == "sBarCSS"){
		var head = document.getElementsByTagName('head')[0];
		head.appendChild(stylesheet);	
	} else {
		jQuery('#sBarCSS').before(stylesheet);
	}
	
}

// <Name> resizeText
// <Purpose> Size the text on the page up/down.
// <ToDo> very buggy. Resizes studybar text, which we dont want.

window.resizeText = function(multiplier) {
	
	if(identifyBrowser() == "IE"){
		//var current = parseFloat((Math.sqrt(parseFloat(jQuery('body').css("font-size"),10).toFixed(2)/12)*12).toFixed(1));
		var current = parseFloat(jQuery('body').css('font-size'));
	} else {
		var current = parseFloat(jQuery('body').css('font-size'));
	}

	var mult = parseFloat(multiplier);
	var newVal = parseFloat(current + mult);

	//alert(current + "+" + mult + "=" + newVal);
	//jQuery('body, p:not-empty, div:not-empty').css('font-size', newVal + "px" );
	jQuery('body').css('font-size', newVal + "px" );
}


window.selectedTTS = function(){
	
	var selectedData = getSelectedText();

	if(selectedData != ""){

		toolbarItems.TTS.clickEnabled = false;
		
		// What method of XHR are we using for this?
		if(XHRMethod == 'GM-XHR'){
			jQuery.facebox.changeFaceboxContent( toolbarItems.TTS.dialogs.starting );
			
			GM_xmlhttpRequest({ method: "POST",
					url: "http://access.ecs.soton.ac.uk/seb/StudyBar/TTS/jobController.php", 
					onload: ttsJobSent,
					headers:{'Content-type':'application/x-www-form-urlencoded'},
	    			data:"page=" + encodeURIComponent(window.location) + "&data=" + encodeURI( b64( getSelectedText() ) )
				});
		} else {
			// Another browser. We'll use the custom xmlhttprequest method.
	
			// Send the data in chunks, as chances are we cant get it all into one request.
			
			var transmitData = b64( getSelectedText() );
			
			var chunks = Math.ceil(transmitData.length / settings.ttsSplitChunkSize);
			
			if(chunks > 0){
				var reqID = Math.floor(Math.random() * 5001);
				
				jQuery.facebox.changeFaceboxContent( "<h2>Processing</h2><p>Compacting and transmitting data...<br /><div id='compactStatus'>0 / " + chunks + "</div></p>" );
				
				sendTTSChunk(transmitData, 1, chunks, reqID);
			} else {
				jQuery.facebox.changeFaceboxContent( "<h2>Oops!</h2><p>There doesn't seem to be any content on this page, or we can't read it.</p>" );
			}
		}
	} else {
		jQuery.facebox("<h2>Text-to-Speech</h2><p>To use the text to speech feature with selected text, please first select the text on this page that you would like to convert. After you have done this, click the Text to Speech button, and select the 'selected text' option.</p>");
	}
}

// <Name> startTTS
// <Purpose> Invoke the Text To Speech engine.

window.startTTS = function(){
	
	toolbarItems.TTS.clickEnabled = false;

	var $sendData = jQuery(document.body).clone();
	
	$sendData.children('#sbar').remove();
	$sendData.children('#sbarGhost').remove();
	$sendData.children('#facebox').remove();
	$sendData.children('script').remove();
	$sendData.children('style').remove();
	$sendData.children('facebox_overlay').remove();
	
	// What method of XHR are we using for this?
	if(XHRMethod == 'GM-XHR'){
		jQuery.facebox.changeFaceboxContent( toolbarItems.TTS.dialogs.starting );
		
		GM_xmlhttpRequest({ method: "POST",
				url: "http://access.ecs.soton.ac.uk/seb/StudyBar/TTS/jobController.php", 
				onload: ttsJobSent,
				headers:{'Content-type':'application/x-www-form-urlencoded'},
    			data:"page=" + encodeURIComponent(window.location) + "&data=" + encodeURI( b64($sendData.html()) )
			});
	} else {
		// Another browser. We'll use the custom xmlhttprequest method.

		// Send the data in chunks, as chances are we cant get it all into one request.
		
		var transmitData = b64($sendData.html());
		
		var chunks = Math.ceil(transmitData.length / settings.ttsSplitChunkSize);
		
		if(chunks > 0){
			var reqID = Math.floor(Math.random() * 5001);
			
			jQuery.facebox.changeFaceboxContent( "<h2>Processing</h2><p>Compacting and transmitting data...<br /><div id='compactStatus'>0 / " + chunks + "</div></p>" );
			
			sendTTSChunk(transmitData, 1, chunks, reqID);
		} else {
			jQuery.facebox.changeFaceboxContent( "<h2>Oops!</h2><p>There doesn't seem to be any content on this page, or we can't read it.</p>" );
		}
	}

}

window.sendTTSChunk = function(fullData, block, totalBlocks, reqID){

	if(block == 1){
		var start = 0;
	} else {
		var start = (settings.ttsSplitChunkSize * block);
	}
	
	if( (start + settings.ttsSplitChunkSize) > fullData.length ){
		var endPoint = fullData.length;
	} else {
		var endPoint = (start + settings.ttsSplitChunkSize);
	}
	
	var payload = fullData.substring(start, endPoint);

	if( block == totalBlocks-1 ){
		var urlString = settings.baseURL + 'xmlhttp/remote.php?rt=tts&id=' + reqID + '&data=' + payload + "&chunkData=" + totalBlocks + "-" + block + "&page=" + encodeURIComponent(window.location);
	} else {
		var urlString = settings.baseURL + 'xmlhttp/remote.php?rt=tts&id=' + reqID + '&data=' + payload + "&chunkData=" + totalBlocks + "-" + block;
	}

	window.attachJS( urlString, 'CS-XHR' );

	this.ttsAjaxInterval = setInterval(function(){
		this.checkCSXHRTTSResponse(fullData, block, totalBlocks, reqID);
	}, 100);	

}

window.checkCSXHRTTSResponse = function(fullData, block, totalBlocks, reqID){
	// Do we have data yet? If so, lets clear the interval and parse the results!
	if( (typeof CSresponseObject) != "undefined" ){
		clearInterval( this.ttsAjaxInterval );
		
		// Copy the response object to a local object.
		var RO = CSresponseObject;

		jQuery("#compactStatus").html(block + " / " + totalBlocks);
				
		if(block == totalBlocks){
			// Finished request..
			
			
			if( (typeof RO.status) != "undefined"){
				jQuery.facebox.changeFaceboxContent( toolbarItems.TTS.dialogs.starting );
				
				window.ttsJobSent( RO );				
				// Remove the response JS.
				jQuery('#CS-XHR').remove();
			} else {
				this.ttsAjaxInterval = setInterval(function(){
					self.waitTTSResponse();
				}, 100);
			}
		} else {
			// Send the next block.
			if(RO.data.message == "ChunkSaved"){
				sendTTSChunk(fullData, (block + 1), totalBlocks, reqID);
			} else {
				jQuery.facebox.changeFaceboxContent("<h2>Error</h2><p>An error occurred on the server. Please try again later.</p>");
			}
		}

	}	
}

window.waitTTSResponse = function(){
	if( (typeof CSresponseObject) != "undefined" ){
		
		console.dir(RO);
		// Copy the response object to a local object.
		var RO = CSresponseObject;

		// Remove the response JS.
		jQuery('#CS-XHR').remove();
	
		if( (typeof RO.status) != "undefined" ){
			clearInterval( this.ttsAjaxInterval );
			jQuery.facebox.changeFaceboxContent( toolbarItems.TTS.dialogs.starting );
			
			window.ttsJobSent( RO );
		} else {
			// Remove the response JS.
			jQuery('#CS-XHR').remove();		
		}
	
	}
}

window.ttsJobSent = function(response){
	
	if(XHRMethod == "GM-XHR"){
		var ro = eval("(" + response.responseText + ")");
	} else {
		var ro = response;
	}	
	
	if(ro.status == "encoding" && ro.status != "failure"){
		if(identifyBrowser() == "FF"){
			window.setTimeout(countdownTTS, 0, (ro.est_completion / ro.chunks), ro.ID );
		} else {
			setTimeout(function(){
				window.countdownTTSCB( (ro.est_completion / ro.chunks), ro.ID );
			}, 0);
		}
	} else if(ro.status == "failure" && ro.reason == "overcapacity"){
		jQuery.facebox("<h2>Oops!</h2> <p>The server is currently over capacity for text to speech conversions. Please try again later.</p>");
	} else if(ro.status == "failure" && ro.message == "") {
		jQuery.facebox("<h2>Oops!</h2> <p>Something went wrong while we were converting this page to text. Please try again shortly.</p>");
	} else {
		jQuery.facebox("<h2>Oops!</h2> <p>" + ro.reason + " " + ro.message + "</p>");
	}
	
}

window.countdownTTS = function(){
	if(isNaN(arguments[0])){
		jQuery.facebox.changeFaceboxContent("<h2>Oops!</h2> <p>Something went wrong while we were converting this page to text.</p>");
	} else {
		if(arguments[0] == 0){
			window.setTimeout(playTTS, 0, arguments[1]);
		} else {
			jQuery('#sbttstimeremaining').html( arguments[0] + " seconds" );
			window.setTimeout(countdownTTS, 1000, (arguments[0] - 1), arguments[1]);
		}
	}
}

window.countdownTTSCB = function(tLeft, id){
	if(isNaN(arguments[0])){
		jQuery.facebox.changeFaceboxContent("<h2>Oops!</h2> <p>Something went wrong while we were converting this page to text.</p>");
	} else {
		if(tLeft == 0){
			setTimeout(function(){ playTTSCB(id) }, 0);
		} else {
			jQuery('#sbttstimeremaining').html( tLeft + " seconds" );
			setTimeout(function(){ countdownTTSCB( (tLeft - 1), id ) }, 1000);
		}
	}
}

window.playTTSCB = function(id){
	embedPlayer(id);
}

window.playTTS = function(){
	embedPlayer(arguments[0]);
}

window.embedPlayer = function(id){
	//jQuery('#sbar').append(jQuery("<embed src=\"" + settings.baseURL + "TTS/player/player-licensed.swf\" width=\"300\" height=\"300\" allowscriptaccess=\"always\" allowfullscreen=\"false\" flashvars=\"file=" + settings.baseURL + "TTS/cache/" + id + ".xml&autostart=true&playlist=bottom&repeat=list\" />") );
	
	if(identifyBrowser() != "IE"){
		jQuery('#sbar').append( jQuery("<div id=\"flashContent\"><OBJECT classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0\" width=\"1\" height=\"1\" id=\"audioe\"> <PARAM name=movie value=\"" + settings.baseURL + "TTS/player/player-licensed.swf\"></PARAM> <PARAM name=flashvars value=\"file=" + settings.baseURL + "TTS/cache/" + id + ".xml&autostart=true&playlist=bottom&repeat=list&playerready=playerReady&id=audioo\"><PARAM name=allowscriptaccess value=\"always\" /><embed type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" src=\"" + settings.baseURL + "TTS/player/player-licensed.swf\" width=\"1\" height=\"1\" allowscriptaccess=\"always\" allowfullscreen=\"false\" flashvars=\"file=" + settings.baseURL + "TTS/cache/" + id + ".xml&autostart=true&playlist=bottom&repeat=list&playerready=playerReady\" name=\"audioe\" /> </OBJECT></div>") );
	} else {
		
		/////
		jQuery("<div id=\"flashContent\"></div>").appendTo("#sbar");
		
		var flashvars = false;
		var params = {
		  flashvars: "file=" + settings.baseURL + "TTS/cache/" + id + ".xml&autostart=true&playlist=bottom&repeat=list&playerready=playerReady&id=audioo",
		  allowscriptaccess: "always"
		};
		var attributes = {
		  id: "audioo",
		  name: "audioo"
		};
		
		
		swfobject.embedSWF(settings.baseURL + "TTS/player/player-licensed.swf", "flashContent", "1", "1", "9.0.0","expressInstall.swf", flashvars, params, attributes);
	}
	jQuery(document).trigger('close.facebox');
}

window.insertTTSControlBox = function(player){
	jQuery("#sb-btn-tts").after( toolbarItems.TTS.extendedButtons.controlBox );
	toolbarItems.TTS.position = 0;
	toolbarItems.TTS.playingItem = 0;
	
	jQuery('#sb-btnico-tts').attr('src', settings.baseURL + "presentation/images/loadingline.gif").css('padding-top', '8px');

	jQuery('#sb-tts-plpaus').tipsy({gravity: 's'});
	jQuery('#sb-tts-stop').tipsy({gravity: 's'});
	jQuery('#sb-tts-rwd').tipsy({gravity: 's'});
	
	mbEventListener('sb-tts-plpaus', 'click', function(e){
		if(identifyBrowser() == "FF" && (typeof StudyBarNoSandbox) == 'undefined'){
			unsafeWindow.document["audioe"].sendEvent('play'); 
		} else {
			//window.document["audioe"].sendEvent('play'); 
			if(identifyBrowser() == "IE") {
				swfobject.getObjectById(SBaudioPlayer).sendEvent('play');
			} else {
				window.document["audioe"].sendEvent('play'); 
			}
		}
	});
	
	mbEventListener('sb-tts-rwd', 'click', function(e){
		var scrubAmount = 2;
		var currentPosition = toolbarItems.TTS.position;
		var newPosition = (currentPosition - scrubAmount);
		if(newPosition < 0) newPosition = 0;
		
		if(identifyBrowser() == "FF" && (typeof StudyBarNoSandbox) == 'undefined'){
			unsafeWindow.document["audioe"].sendEvent('seek', newPosition); 
		} else {
			if(identifyBrowser() == "IE") {
				swfobject.getObjectById(SBaudioPlayer).sendEvent('seek', newPosition); 
			} else {
				window.document["audioe"].sendEvent('seek', newPosition); 
			}
		}
	});
	
	mbEventListener('sb-tts-stop', 'click', function(e){
		if(identifyBrowser() == "FF" && (typeof StudyBarNoSandbox) == 'undefined'){
			unsafeWindow.document["audioe"].sendEvent('stop');
		} else {
			//window.document["audioe"].sendEvent('stop');
			if(identifyBrowser() == "IE") {
				swfobject.getObjectById(SBaudioPlayer).sendEvent('stop');
			} else {
				window.document["audioe"].sendEvent('stop');
			}
		} 
		
		removeControlBox();

	});
}


window.removeControlBox = function(){
      	jQuery("#sbar #sbAudioControlBox").remove();
      	jQuery("#flashContent").remove();
      	jQuery('#sb-btnico-tts').attr('src', settings.baseURL + "presentation/images/sound.png").css('padding-top', '6px');
      	jQuery(".tipsy").remove();
      	toolbarItems.TTS.clickEnabled = true;
}


if(identifyBrowser() == "FF" && (typeof StudyBarNoSandbox) == 'undefined'){
	unsafeWindow.playerReady = function(obj) {
		insertTTSControlBox(obj);
		SBaudioPlayerID = unsafeWindow.document.getElementById(obj.id);
		unsafeWindow.document["audioe"].addModelListener("STATE", "SBAudioStateListener");
		unsafeWindow.document["audioe"].addModelListener("TIME", "SBAudioTimeMonitor");
		unsafeWindow.document["audioe"].addControllerListener("ITEM", "SBAudioItemMonitor");
	}
	
	unsafeWindow.SBAudioTimeMonitor = function(obj){
		toolbarItems.TTS.position = obj.position;
	}
	
	unsafeWindow.SBAudioItemMonitor = function(obj){
		toolbarItems.TTS.playingItem = obj.index;
	}
	
	unsafeWindow.SBAudioStateListener = function(obj) {
		var state = obj.newstate;
	
		if(state == "COMPLETED" && (toolbarItems.TTS.playingItem + 1) == (unsafeWindow.document['audioe'].getPlaylist().length - 1)){
			// Completed, remove controlbox and reset everything back to normal.
			removeControlBox();
		} else if(state == "IDLE" || state == "PAUSED") {
			jQuery('#sb-btnico-plpaus').attr('src', settings.baseURL + "presentation/images/control.png");
			jQuery('#sb-btnico-tts').attr('src', settings.baseURL + "presentation/images/sound.png").css('padding-top', '6px');
		} else {
			if(toolbarItems.TTS.clickEnabled == false){
				jQuery('#sb-btnico-plpaus').attr('src', settings.baseURL + "presentation/images/control-pause.png");
				jQuery('#sb-btnico-tts').attr('src', settings.baseURL + "presentation/images/loadingline.gif").css('padding-top', '8px');
			}
		}	
	}
} else {
	window.playerReady = function(obj) {
		insertTTSControlBox(obj);

		SBaudioPlayer = obj.id;
		
		if(identifyBrowser() == "IE") {
			swfobject.getObjectById(obj.id).addModelListener("STATE", "SBAudioStateListener");
		
			swfobject.getObjectById(obj.id).addModelListener("TIME", "SBAudioTimeMonitor");
			swfobject.getObjectById(obj.id).addControllerListener("ITEM", "SBAudioItemMonitor");		
		} else {
			window.document["audioe"].addModelListener("STATE", "SBAudioStateListener");
			window.document["audioe"].addModelListener("TIME", "SBAudioTimeMonitor");
			window.document["audioe"].addControllerListener("ITEM", "SBAudioItemMonitor");
		}
	}	
	
	window.SBAudioTimeMonitor = function(obj){
		toolbarItems.TTS.position = obj.position;
	}
	
	window.SBAudioItemMonitor = function(obj){
		toolbarItems.TTS.playingItem = obj.index;
	}
	
	window.SBAudioStateListener = function(obj) {
		var state = obj.newstate;
	
		if(state == "COMPLETED" && (toolbarItems.TTS.playingItem + 1) == (swfobject.getObjectById(obj.id).getPlaylist().length - 1)){
			// Completed, remove controlbox and reset everything back to normal.
			removeControlBox();
		}
		
		if(state == "IDLE" || state == "PAUSED") {
			jQuery('#sb-btnico-plpaus').attr('src', settings.baseURL + "presentation/images/control.png");
			jQuery('#sb-btnico-tts').attr('src', settings.baseURL + "presentation/images/sound.png").css('padding-top', '6px');
		} else {
			if(toolbarItems.TTS.clickEnabled == false){
				jQuery('#sb-btnico-plpaus').attr('src', settings.baseURL + "presentation/images/control-pause.png");
				jQuery('#sb-btnico-tts').attr('src', settings.baseURL + "presentation/images/loadingline.gif").css('padding-top', '8px');
			}
		}	
	}
}


// <Name> spellCheckPage
// <Purpose> Invoke the modified jQuery spellchecking engine that we're using. Hook onto all textarea and text fields.
// <ToDo> Does not work first invoke for some reason. Possibly to do with xmlhttprequest change for GM?

window.spellCheckPage = function(){
	//if(toolbarItems.spell.checkerEnabled == false){
		
		if((typeof unsafeWindow) != 'undefined'){
			// Are there any TinyMCE fields on this page?
			if((typeof unsafeWindow.tinyMCE) != 'undefined'){
				spellCheckTMCE(unsafeWindow.tinyMCE);
			}
			
			if((typeof unsafeWindow.CKEDITOR) != 'undefined'){
				spellCheckCKE(unsafeWindow.CKEDITOR);
			}
		} else {
			if((typeof window.tinyMCE) != 'undefined'){
				spellCheckTMCE(window.tinyMCE);
			}	
			
			if((typeof window.CKEDITOR) != 'undefined'){
				spellCheckCKE(window.CKEDITOR);
			}	
		}
		
		jQuery("textarea").spellcheck({ useXHRMethod: XHRMethod, baseURL: settings.baseURL });
		jQuery('input[type=text]').spellcheck({ useXHRMethod: XHRMethod, baseURL: settings.baseURL });
		
		jQuery('#sb-btnico-spell').attr('src', settings.baseURL + "presentation/images/spell.png");
		toolbarItems.spell.checkerEnabled = true;
		
		

		
	/*} else {
		alert('removing spellcheck');
		
		jQuery('textarea').spellcheck({ spellEnabled:false });
		
		jQuery('textarea').unbind('keypress blur paste');
		jQuery('textarea').removeData('spellchecker');
		
		jQuery('input[type=text]').unbind('keypress blur paste');
		jQuery('input[type=text]').removeData('spellchecker');		
		
		jQuery('#sb-btnico-spell').attr('src', settings.baseURL + "presentation/images/spell-off.png");
		toolbarItems.spell.checkerEnabled = false;
	}*/
}

window.spellCheckTMCE = function(tinyMCE){
	//check for plugins.spellchecker
	tinyMCE.activeEditor.onKeyPress.add(function(ed, e) {
		var content = tinyMCE.activeEditor.getContent();
		if ( rteSpellTimer ) window.clearTimeout(rteSpellTimer);
		rteSpellTimer = window.setTimeout(function() { jQuery("#" + tinyMCE.activeEditor.editorContainer).rteSpellCheck(content, tinyMCE.activeEditor, { useXHRMethod: XHRMethod, RTEType: 'tMCE' }); }, 750);
	});
}

window.spellCheckCKE = function(CKE){

	for(var o in CKE.instances){
	   	CKE.instances[o].document.on('keypress', function(){
    		if ( rteSpellTimer ) window.clearTimeout(rteSpellTimer);
    		var content = CKE.instances[o].getData();
    		rteSpellTimer = window.setTimeout(function() { jQuery("#" + CKE.instances[o].element.getId()).rteSpellCheck(content, CKE.instances[o], { useXHRMethod: XHRMethod, RTEType: 'CKE' }); }, 750);
    	});
	}


}


// <Name> changeColours
// <Purpose> Controls the faceBox menus invoked by the change styles button in the main bar. Levels are sequential, i.e. 1, 11, 12, 121, 2, 21, 22
// <ToDo> Nothing.

window.changeColours = function(level){
	// Remember to set up any child buttons when loading a dialog, you MUST set onclicks programatically here!
	if(level == 0){
		jQuery.facebox( toolbarItems.CSS.dialogs.colourDialog );
		
		mbEventListener('sbColourChange', "click", function(e){ changeColours(1); } );
		mbEventListener('sbChangeSiteColours', "click", function(e){ changeColours(2); } );
		mbEventListener('sbAttachCSSStyle', "click", function(e){ changeColours(3) } );
		
		jQuery("#sbColourChange").focus();
	}
	if(level == 1){
		jQuery.facebox.changeFaceboxContent( toolbarItems.CSS.dialogs.sbColourDialog );
		mbEventListener('sbRandomColour', "click", function(e){ setColour("rand"); });
		mbEventListener('sbSetColour', "click", function(e){ setColour( jQuery("#sbbackgroundcolour").val() ); });
		mbEventListener('sbColourReset', "click", function(e){ setColour("white"); });
		
		jQuery("#sbbackgroundcolour").focus();
	}
	if(level == 2){
		// Site colours
		jQuery.facebox.changeFaceboxContent( toolbarItems.CSS.dialogs.sbSiteColours );
		mbEventListener('applyPageColours', "click", function(e){ 			
			if( jQuery('#sbtextcolour').val() != "undefined" && jQuery('#sbtextcolour').val() != "original" ){
				jQuery('body, p, div, h1, h2, h3, h4, h5, label').css('color', "#" + jQuery('#sbtextcolour').val());
			}
			
			if( jQuery('#sblinkcolour').val() != "undefined" && jQuery('#sblinkcolour').val() != "original" ){
				jQuery('a').css('color', "#" + jQuery('#sblinkcolour').val());
			}
		});
		
		mbEventListener('sblinkcolour', 'keypress', function(e){ 
			if(e.keyCode == 13){  
				if( jQuery('#sbpagebackgroundcolour').val() != "undefined"){
					document.body.style.backgroundColor = jQuery('#sbpagebackgroundcolour').val();
				}
				
				if( jQuery('#sbtextcolour').val() != "undefined" && jQuery('#sbtextcolour').val() != "original" ){
					jQuery('body').css('color', "#" + jQuery('#sbtextcolour').val());
				}
				
				if( jQuery('#sblinkcolour').val() != "undefined" && jQuery('#sblinkcolour').val() != "original" ){
					jQuery('a').css('color', "#" + jQuery('#sblinkcolour').val());
				}	
				
				jQuery(document).trigger('close.facebox');		
			} 
		
		});
		
		jQuery("#sbtextcolour").focus();
		
	}
	if(level == 3){
		jQuery.facebox.changeFaceboxContent( toolbarItems.CSS.dialogs.sbAttachCSS );
		mbEventListener('sbApplyCSS-yb', "click", function(e){ 
			jQuery(document).trigger('close.facebox');
			removeCSS();
			attachCSS(settings.baseURL + "presentation/stylesheets/high-yo.css", "highvis-yo");
		});
		
		mbEventListener('sbApplyCSS-wb', "click", function(e){ 
			jQuery(document).trigger('close.facebox');
			removeCSS();
			attachCSS(settings.baseURL + "presentation/stylesheets/high-wb.css", "highvis-wb");
			
		});
		
		mbEventListener('sbApplyCSS-wbw', "click", function(e){
			jQuery(document).trigger('close.facebox');
			removeCSS();
			attachCSS(settings.baseURL + "presentation/stylesheets/high-bw.css", "highvis-wbw");
		});
		
		mbEventListener('sbApplyCSS-by', "click", function(e){
			jQuery(document).trigger('close.facebox');
			removeCSS();
			attachCSS(settings.baseURL + "presentation/stylesheets/high-by.css", "highvis-by");
		});
		
		mbEventListener('sbApplyCSS-gw', "click", function(e){
			jQuery(document).trigger('close.facebox');
			removeCSS();
			attachCSS(settings.baseURL + "presentation/stylesheets/high-gw.css", "highvis-by");
		});		
		
		
		
		jQuery("#sbApplyCSS-wb").focus();
	}
}


window.cloneBodyToDiv = function(){
	var $duplicateData = jQuery(document.body).clone();
	var barData = jQuery("<div id=\"sbarGhost\">" + $duplicateData.children('#sbarGhost').html() + "</div><div id=\"sbar\">" + $duplicateData.children('#sbar').html() + "</div>");
	
	$duplicateData.children('#sbar').remove();
	$duplicateData.children('#sbarGhost').remove();
	$duplicateData.children('#facebox').remove();
	$duplicateData.children('facebox_overlay').remove();
	
	jQuery('body').empty();
	
	jQuery('body').html(barData);
	jQuery('body').append("<div id=\"sBarCSSContainer\"></div>");
	
	jQuery('#sBarCSSContainer').html($duplicateData.html());
}

window.removeCSS = function(){
	jQuery('link[rel=stylesheet][id!=sBarCSS]').remove();
}


window.getDictionaryRef = function(){
	var data = eval("\"" + getSelectedText("true") + "\";");

	if(data != ""){
		jQuery("#sb-btnico-dictionary").attr('src', settings.baseURL + "presentation/images/loading.gif");
		
		// Firefox greasemonkey cross domain XMLHTTP.
		if(XHRMethod == 'GM-XHR'){
		
			window.setTimeout(function(){
				GM_xmlhttpRequest({ method: "GET",
					url: "http://en.wiktionary.org/w/api.php?action=query&titles=" + encodeURI(data.toLowerCase()) + "&prop=revisions&rvlimit=1&rvprop=content&format=json", 
					onload: getDictionaryResponse
				});
			}, 0);
		
		} else if(XHRMethod == 'CS-XHR'){

			window.attachJS( settings.baseURL + 'xmlhttp/remote.php?rt=dict&id=' + Math.floor(Math.random() * 5001) + '&action=query&titles=' + encodeURI(data.toLowerCase()) + '&prop=revisions&rvlimit=1&rvprop=content&format=json', 'CS-XHR' );
						
			//alert("XS-XHR/GM-XHR not supported. Switching to " + XHRMethod);
			this.dictAjaxInterval = setInterval(function(){
				window.checkCSXHRDictResponse();
			}, 100);
			
		}
	} else {
		jQuery.facebox("<h2>Dictionary</h2><p>To use the dictionary select a word on the page and click the dictionary button.</p>");
		jQuery("#sb-btnico-dictionary").attr('src', settings.baseURL + "presentation/images/" + toolbarItems.dictionary.ico);
	}
}


window.checkCSXHRDictResponse = function(){
	// Do we have data yet? If so, lets clear the interval and parse the results!
	if( (typeof CSresponseObject) != "undefined" && CSresponseObject != false ){
		clearInterval( this.dictAjaxInterval );

		// Copy the response object to a local object.
		RO = CSresponseObject;
		
		// Remove the response JS.
		jQuery('#CS-XHR').remove();

		window.getDictionaryResponse( RO.data );
		
		// Clear the RO.
		delete RO;
		CSresponseObject = false;
		
	}		
}

window.getDictionaryResponse = function(response){

	if(XHRMethod == "GM-XHR"){
		var ro = eval("(" + response.responseText + ")");
	} else {
		var ro = eval("(" + response + ")");
	}
	
	for(var result in ro.query.pages){
		if(result > -1){
			var definition = eval("ro.query.pages[\"" + result + "\"].revisions[0][\"*\"];");
			var title = eval("ro.query.pages[\"" + result + "\"].title;");
			// Format the wikicode into something we can read in HTML.
			//console.log(definition);
			
			// Replace headings.
			definition = parseDictionaryResponse(definition);
		} else {
			var definition = "Unknown word";
			var title = eval("ro.query.pages[\"-1\"].title;");
		}
	}
	
	jQuery.facebox("<h2>Dictionary Definition for \"" + title + "\"</h2><div class=\"constrainContent\">" + definition + "</div>");
	jQuery("#sb-btnico-dictionary").attr('src', settings.baseURL + "presentation/images/" + toolbarItems.dictionary.ico);
}


window.parseDictionaryResponse = function(input){
	
	// Remove translations blocks.
	output = input.replace(/^((?:={2,})+translations(?:={2,})+.*?)((?:={2,}).*?(?:={2,})|(?:-{4}))/ig, '$2');	
	
	// Replace headings.
	var output = output.replace(/(={2,})+(.*?)(?:={2,})+/ig, function(match, g1, g2, position, input) {
    	return "<h" + g1.length + ">" + g2 + "</h" + g1.length + ">";
    });
    
    // Remove comments
    var output = output.replace(/(<!--.*?-->)/ig, '');
	
	// Replace bold / italics.
	output = output.replace(/(('+){1}(.*?)(?:'+){1})/ig, function(match, g1, g2, g3, position, input){
		switch(g2.length){
		
			case 2:
				return "<em>" + g3 + "</em>";
			break;
			
			case 3:
				return "<b>" + g3 + "</b>";
			break;
			
			case 5:
				return "<em><b>" + g3 + "</b></em>";
			break;
		}
	
	});
	
	// Replace text in curley brackets.
	output = output.replace(/(\{\{(?:(.*?)\|)+(.*?)\}\})/ig, function(match, g1, g2, g3, position, input){
		switch(g2.toLowerCase()){
			case 'also':
				return "See also: " + g3;
			break;
			
			case 'ipa':
				return g3;
			break;
			
			case 'audio':
				return "";
			break;
			
			default:
				return g3;	
			break;
		}
	});
	
	// Replace lists.
	output = output.replace(/([#|\*]+(.*?)\n)/ig, '<li>$2</li>');
	
	// Replace unmatched doublebraces.
	output = output.replace(/(\{\{(\w{1,})\}\})/ig, "<i>$2</i>");
	
	output = output.replace(/(\[\[(.*?)\]\])/ig, "$2");
	
	return output;
}

window.getSelectedText = function(strip){
    var text = '';
     if (window.getSelection){
        text = window.getSelection();
     } else if (document.getSelection){
        text = document.getSelection();
     } else if (document.selection){
        text = document.selection.createRange().text;
     }
    if(strip == "true"){
		return String(text).replace(/([\s]+)/ig, '');
	} else {
		return String(text);
	}
}

// <Name> setColour
// <Purpose> Sets the colour of studybar, either a random colour, or one specified by the user.
// <ToDo> Nothing.

window.setColour = function(code){
	if(code == "rand"){
		colour = '#'+Math.floor(Math.random()*16777215).toString(16);
		jQuery("#sbbackgroundcolour").val(colour);
	} else {
		colour = code;
	}
	jQuery('#sbar').css('background-color', colour);
	
	//document.getElementById('sbar').style.backgroundColor = colour;
}

// <Name> ttsOptions
// <Purpose> Load the TTS options dialog.

window.ttsOptions = function(){
	if(toolbarItems.TTS.clickEnabled == true){
		jQuery.facebox( toolbarItems.TTS.dialogs.options );
		mbEventListener('sbStartTTS', 'click', function(e){ startTTS() } );
		mbEventListener('sbStartTTSSelection', 'click', function(e){ selectedTTS() });
		
		if(jQuery("#sb-swfo").length == 0 && (typeof StudyBarNoSandbox) != 'undefined') attachJS( settings.clientRoot + 'channels/' + relChannel + '/swfobject.js', 'sb-swfo' );
		// This works, but stops TTS for selected text...
		//jQuery("#sbStartTTS").focus();
	}
}

window.resetPage = function(){
	location.reload(true);
}


window.fontSettingsDialog = function(){
	jQuery.facebox( toolbarItems.fontSettings.dialogs.main );
	mbEventListener('sbfontfaceapply', 'click', function(e){
		if(jQuery('#sbfontface').val() != "--Site Specific--") jQuery('body').css('font-family', jQuery('#sbfontface').val());
		jQuery('div[class!=sbarDialogButton]').css('line-height', jQuery('#sblinespacing').val() + "%");
		jQuery('#sbar').css('line-height', '0%');
		//jQuery('#sbar').nextAll()
	});
	
	jQuery("#sbfontface").focus();
	//sbfontfaceapply
}


window.referencesDialog = function(){
	var htmlData = toolbarItems.references.dialogs.landingDialog;
	var url = String(window.location);
	
	var options = "<option>Webpage</option>";
	
	if( url.match("wiki") != null ) alert("Warning: This source may not be suitable for academic writing.");

	if( url.match("news") != null){
		// This may be a news site.
		options += "<option selected=\"selected\">News Article</option>";	
	} else {
		options += "<option>News Article</option>";
	}
	
	htmlData = htmlData.replace(/(<select id=\"sbReferenceType\">)(\s)(<\/select>)/ig, "$1" + options + "$3");
	
	jQuery.facebox( htmlData );

	mbEventListener('sbScanReferences', 'click', function(e){ scanForReferenceMaterial( jQuery('#sbReferenceType').val() ); } );

}

window.scanForReferenceMaterial = function(type){

	var emptySelect = "<select id=\"{{id}}\">{{data}}</select>";
	var outputHTML = toolbarItems.references.dialogs.results;

	var $bodyString = jQuery(document.body).clone();
	jQuery($bodyString).children('#facebox').remove();
	var bodyString = $bodyString.html();
	// Author
	
		// Individual? 
		//By[ :]?([a-zA-Z]{3,}(?:[\s]?[a-zA-Z]{3,}))[.*?]?
		var authMatch = bodyString.match(/By[:]?[\s]{1,}(?:([a-zA-Z]{3,}(?:[\s]?[a-zA-Z]{3,}))|(?:(?:<.*?>)([a-zA-Z]{3,}(?:[\s]?[a-zA-Z]{3,}))(?:<.*?>)))/ig);
		
		if(authMatch == null) {
			// Corporate author, maybe?
			outputHTML = outputHTML.replace('{{author}}', "");
		} else {
			
			var authorUniqueMatches = authMatch.unique();
			
			if(authorUniqueMatches.length == 1){
				outputHTML = outputHTML.replace('{{author}}', authorUniqueMatches[0].replace(/(By[:]?[\s]{1,})/ig, ''));
			} else {
				// Multiple matches found.
				
				var matchOptions = "";
				
				for(i = 0; i < authorUniqueMatches.length; i++){
					var thisMatch = authorUniqueMatches[i];
					matchOptions += "<option>" + thisMatch.replace(/(By[:]?[\s]{1,})/ig, '') + "</option>";
				}
				
				var authorSelect = emptySelect.replace("{{data}}", matchOptions);
				authorSelect = authorSelect.replace("{{id}}", "sbAuthorSelect");
				
				authorSelect += " <a id=\"sbAuthorSelectAccept\" href=\"#s-b-c\"><img src=\"" + settings.baseURL + "/presentation/images/accept.png\" /></a>";
				
				outputHTML = outputHTML.replace('{{author}}', authorSelect);
			}
		}
	
	// Page Title
	var titleMatch = bodyString.match(/(?:<h[1-2]>[\s]?(.*?)[\s]?<\/h[1-2]>)/igm);
	
	if(titleMatch == null){
		outputHTML = outputHTML.replace("{{ptitle}}", document.title);
	} else {
		var titleUniqueMatches = titleMatch.unique();
		
		var matchOptions = "<option>" + document.title + "</option>";
		
		for(i = 0; i < titleUniqueMatches.length; i++){
			var thisMatch = titleUniqueMatches[i];
			if(thisMatch != "References") matchOptions += "<option>" + thisMatch + "</option>";
		}
		
		var titleSelect = emptySelect.replace("{{data}}", matchOptions);
		titleSelect = titleSelect.replace("{{id}}", "sbTitleSelect");
		
		titleSelect += " <a id=\"sbTitleSelectAccept\" href=\"#s-b-c\"><img src=\"" + settings.baseURL + "/presentation/images/accept.png\" /></a>";
		
		outputHTML = outputHTML.replace("{{ptitle}}", titleSelect);
	}
	
	
	// Date
	var dateMatch = bodyString.match(/(([\w]{3,8} [\d]{1,2}[,]? [\d]{4})|([0-3][0-9]\/[0-3][0-9]\/[\d]{2,4})|([\d]{2} [\w]{3,8} [\d]{4}))/ig);
	
	if(dateMatch == null){
		outputHTML = outputHTML.replace("{{date}}", "");
	} else {
		var dateUniqueMatches = dateMatch.unique();
	
		if(dateUniqueMatches.length == 1){
			outputHTML = outputHTML.replace("{{date}}", dateUniqueMatches);
		} else {
			var matchOptions = "";
			
			for(i = 0; i < dateUniqueMatches.length; i++){
				var thisMatch = dateUniqueMatches[i];
				matchOptions += "<option>" + thisMatch + "</option>";
			}
			
			var dateSelect = emptySelect.replace("{{data}}", matchOptions);
			dateSelect = dateSelect.replace("{{id}}", "sbDateSelect");
			
			dateSelect += " <a id=\"sbDateSelectAccept\" href=\"#s-b-c\"><img src=\"" + settings.baseURL + "/presentation/images/accept.png\" /></a>";
			
			outputHTML = outputHTML.replace('{{date}}', dateSelect);
		}
	}

	
	// Name of website
	var thisDomain = document.domain;
	thisDomain = thisDomain.match(/([\w]{3,}\.[\w].*){1}$/);	
	
	outputHTML = outputHTML.replace("{{wsname}}", thisDomain[1]);
	
	outputHTML = outputHTML.replace( "{{url}}", window.location );
	outputHTML = outputHTML.replace( "{{accessed}}", Date() );
	
	jQuery.facebox.changeFaceboxContent( outputHTML );
	
	// Run select box listener attachments.
	if(authorUniqueMatches != null) {
		if(authorUniqueMatches.length > 1){
			jQuery('#sbAuthorSelect').bind('change', function(e){
				jQuery('#sbAuthorSelect').replaceWith( jQuery('#sbAuthorSelect').val() );
				jQuery('#sbAuthorSelectAccept').remove();
			});
			
			jQuery('#sbAuthorSelectAccept').bind('click', function(e){
				jQuery('#sbAuthorSelect').replaceWith( jQuery('#sbAuthorSelect').val() );
				jQuery('#sbAuthorSelectAccept').remove();
			});
		}
	}
	
	if(titleUniqueMatches != null) {
		if(titleUniqueMatches.length >= 1){
			jQuery('#sbTitleSelect').bind('change', function(e){
				jQuery('#sbTitleSelect').replaceWith( jQuery('#sbTitleSelect').val() );
				jQuery('#sbTitleSelectAccept').remove();
			});
			
			jQuery('#sbTitleSelectAccept').bind('click', function(e){
				jQuery('#sbTitleSelect').replaceWith( jQuery('#sbTitleSelect').val() );
				jQuery('#sbTitleSelectAccept').remove();
			});
		}
	}

	if(dateUniqueMatches != null) {
		if(dateUniqueMatches.length > 1){
			jQuery('#sbDateSelect').bind('change', function(e){
				jQuery('#sbDateSelect').replaceWith( jQuery('#sbDateSelect').val() );
				jQuery('#sbDateSelectAccept').remove();
			});
			
			jQuery('#sbDateSelectAccept').bind('click', function(e){
				jQuery('#sbDateSelect').replaceWith( jQuery('#sbDateSelect').val() );
				jQuery('#sbDateSelectAccept').remove();
			});
		}
	}

}

// Dialog for closing the bar.
window.unloadOptions = function(){
	jQuery.facebox(sysDialog.closeDialog);
	mbEventListener('sbCloseAllSites', 'click', function(e){ 
		jQuery(document).trigger('close.facebox');
		window.setTimeout(function(){ unloadStudyBar(true) }, 500);
	} );
}

// <Name> unloadStudyBar
// <Purpose> Unloads studybar, resets page back to normal and sets studybar not to load next time.
// <ToDo> Save settings to central server? local?

window.unloadStudyBar = function(all){	
	// Closing studybar.
	if( (typeof GM_setValue) == 'undefined' || identifyBrowser() == "Opera" ){
		// No greasemonkey.
		
		// Remove the injected javascript files.
		jQuery('#sb-spell').remove();
		jQuery('sb-tipsy').remove();
		jQuery('sb-facebox').remove();
		jQuery('sb-buttons').remove();
		jQuery('sb-gmcompat').remove();
		
		//Remove last
		jQuery('#sb-jquery').remove();
	} else {
		if(all == true) window.setTimeout(GM_setValue, 0, "autoload", false);
	}
	
	// Remove the divs for StudyBar from the page.
	
	//jQuery('#facebox').remove();
	//jQuery('#facebox_overlay').remove();
	jQuery('#sbarGhost').remove();
	jQuery('#sbar').remove();
	//jQuery('#sBarCSS').remove();
	jQuery('.tipsy').remove();
	
	settings.invoked = false;
}

window.studybarHelp = function(){
	window.location = "http://access.ecs.soton.ac.uk/StudyBar/functions";
}

// <Name> createIEaddEventListeners
// <Purpose> Wrap addEventListeners to attachEvent for IE.

window.createIEaddEventListeners = function(){
    if (document.addEventListener || !document.attachEvent) return;

    function ieAddEventListener(eventName, handler, capture){
        if (this.attachEvent) this.attachEvent('on' + eventName, handler);
    }

    function attachToAll(){
        var i, l = document.all.length;

        for (i = 0; i < l; i++)
            if (document.all[i].attachEvent)
                document.all[i].addEventListener = ieAddEventListener;
    }

    var originalCreateElement = document.createElement;

    document.createElement = function(tagName){
        var element = originalCreateElement(tagName);
        
        if (element.attachEvent)
            element.addEventListener = ieAddEventListener;

        return element;
    }
    
    window.addEventListener = ieAddEventListener;
    document.addEventListener = ieAddEventListener;

    var body = document.body;
    
    if (body){
        if (body.onload){
            var originalBodyOnload = body.onload;

            body.onload = function(){
                attachToAll();
                originalBodyOnload();
            };
        } else {
            body.onload = attachToAll;
        }
    } else {
        window.addEventListener('load', attachToAll);
    }
}
	
Array.prototype.unique = function () {
	var r = new Array();
	o:for(var i = 0, n = this.length; i < n; i++){
		for(var x = 0, y = r.length; x < y; x++){
			if(r[x]==this[i]) continue o;
		}
		r[r.length] = this[i];
	}
	return r;
}

window.b64 = function(input) {
	// + == _
	// / == -
	var bkeys = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_-=";
	var output = "";
	var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
	var i = 0;

	input = utf8_encode(input);

	while (i < input.length) {

		chr1 = input.charCodeAt(i++);
		chr2 = input.charCodeAt(i++);
		chr3 = input.charCodeAt(i++);

		enc1 = chr1 >> 2;
		enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
		enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
		enc4 = chr3 & 63;

		if (isNaN(chr2)) {
			enc3 = enc4 = 64;
		} else if (isNaN(chr3)) {
			enc4 = 64;
		}

		output = output +
		bkeys.charAt(enc1) + bkeys.charAt(enc2) +
		bkeys.charAt(enc3) + bkeys.charAt(enc4);

	}

	return output;
}


window.utf8_encode = function(string) {
	string = string.replace(/\r\n/g,"\n");
	var utftext = "";

	for (var n = 0; n < string.length; n++) {

		var c = string.charCodeAt(n);

		if (c < 128) {
			utftext += String.fromCharCode(c);
		} else if((c > 127) && (c < 2048)) {
			utftext += String.fromCharCode((c >> 6) | 192);
			utftext += String.fromCharCode((c & 63) | 128);
		} else {
			utftext += String.fromCharCode((c >> 12) | 224);
			utftext += String.fromCharCode(((c >> 6) & 63) | 128);
			utftext += String.fromCharCode((c & 63) | 128);
		}

	}

	return utftext;
}


// <Name> mbEventListener
// <Purpose> Multiple Browser event listener handler. Handles differences between IE & all other browsers in how they attach events to objects.

window.mbEventListener = function(target, event, fnc){
	 if (identifyBrowser() != "IE"){
	 	// FFX / Opera / Chrome
	 	document.getElementById(target).addEventListener(event, fnc, true);
	 } else {
	 	// IE
	 	document.getElementById(target).attachEvent('on' + event, fnc);
	 }
}

// <Name> setXHRMethod
// <Purpose> Set a global for the XmlHTTPRequest method that we are using, so that it can be used elsewhere in the script.

window.setXHRMethod = function(){
	// Is this firefox with Greasemonkey installed?
	if( identifyBrowser() == "FF" && (typeof GM_registerMenuCommand) && (typeof StudyBarNoSandbox) == 'undefined' ){
		XHRMethod = 'GM-XHR';
	} else {
		// Use the custom method of
		XHRMethod = 'CS-XHR';
	}
}

function isLoaded(){
	if(document.getElementById('sbar') != null){
		return true;
	} else {
		return false;
	}
}

// <Name> loadJQExtensions
// <Purpose> Load the jQuery extensions required. Load in order, so they do not conflict.

window.loadJQExtensions = function(){
	// Load jQuery extensions in order.
	attachJS( settings.clientRoot + 'channels/' + relChannel + '/jquery.tipsy.js', 'sb-tipsy' );
	attachJS( settings.clientRoot + 'channels/' + relChannel + '/jquery.spell.js', 'sb-spell' );
	attachJS( settings.clientRoot + 'channels/' + relChannel + '/jquery.facebox.js', 'sb-facebox' );
}

// Start the loading process. We need jQuery to be loaded before we can do anything else, so check to see if its loaded. If its not, wait another 100ms and try again. Otherwise, load jQuery extensions and then studybar itself.
window.startProcess = function(){
	if ( typeof(jQuery) == 'undefined' ) {
		setTimeout('startProcess()', 100);
	} else {
		loadJQExtensions();
		
		bootstrap();
	}

}

function showLoader(){
	// Attatch our new stylesheet.
	attachCSS(settings.baseURL + settings.stylesheetURL, "sBarCSS");
	// Create the div for the StudyBar ghost.
	barGhost = document.createElement('div');
	// Set the ID of the toolbar.
	barGhost.id = "sbarGhost";
	barGhost.innerHTML = "<center><img src=\"" + settings.baseURL + "presentation/images/loading.gif\" style=\"margin-top:10px;\" /></center>";
	
	// Insert it as the first node in the body node.
	document.body.insertBefore(barGhost, document.body.firstChild);
}

function bootstrap(){
	// We've loaded essential dependencies, start the actual loading procedure.
	loadStudyBar();
}

///////////////////////////////////////////////////////////////////////////
// Below is the loading code.
///////////////////////////////////////////////////////////////////////////
// Are we loading in an iframe?
if (window == window.top) {

	if( isLoaded() == false ){
	
		// Check to see if we can register a menu item in Greasemonkey. Note: This is only supported in Greasemonkey for Firefox.
		if( (typeof GM_registerMenuCommand) == 'undefined' || (typeof StudyBarNoSandbox) != 'undefined') {
			// No greasemonkey extensions. Load manually.

			// Show the sBarGhost loader.
			showLoader();
			
			// IE? Change over our event listeners to its own format.
			createIEaddEventListeners();
			
			// Attach jQuery, our custom button class and the greasemonkey compatibility functions.
			attachJS( 'http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js', 'sb-jquery' );
			// Our button class.
			attachJS( settings.clientRoot + 'channels/' + relChannel + '/button.class.js', 'sb-buttons' );
			
			startProcess();
		} else {
			
			// If the user had studybar open, reopen it again.
			var autoLoadValue = GM_getValue('autoload', false);

			if( autoLoadValue == true ) {
				// Show the sBarGhost loader.
				showLoader();
				
				jQuery(document).ready(function(){
					bootstrap();
				});
			}
			
			// Register the greasemonkey menu items.
			GM_registerMenuCommand("Load ATbar", function(){ 
				// Show the sBarGhost loader.
				showLoader();
				// Load StudyBar
				jQuery(document).ready(function(){
					bootstrap();
				});
			});
		
		
		}
	}
	
}
