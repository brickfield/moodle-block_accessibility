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
 window.returnNewButton = function(id, ico, act, tip, sClass, baseURL){

	var buttonItem = {
		conf : { 
			id: id, icon: ico, action: act, tooltip: tip, HTML: "", styleClass: sClass,
			template: "<div id=\"sb-btn-(ID)\" class=\"sb-btn(CLASS)\"><a title=\"(TITLE)\" id=\"sb-lnk-(ID)\" href=\"#s-b-c\"><img id=\"sb-btnico-(ID)\" src=\"(URL)\" alt=\"(TITLE)\" border=\"0\" /></a></div> " 
		},
		
		addListener: function(){
			jQuery("#sb-lnk-" + buttonItem.conf.id).bind("click", function(e){ eval(act) });
		},
		
		writeHTML: function(){
			var output = buttonItem.buildHTML();
			
			if( buttonItem.conf.id == "closeSBar" ){
				jQuery( jQuery("#sbar .fright").get(0) ).before(output);
			} else {
				jQuery(output).appendTo('#sbar');
			}
			
			jQuery('#sb-lnk-' + buttonItem.conf.id).tipsy({gravity: 's'});
			//jQuery('#sb-lnk-' + buttonItem.conf.id).removeAttr('title');
			buttonItem.addListener();
		},
		
		buildHTML: function(){
			tmpHTML = buttonItem.conf.template.replace(/\(TITLE\)/ig, buttonItem.conf.tooltip);
			tmpHTML = tmpHTML.replace(/\(ID\)/ig, buttonItem.conf.id);
			tmpHTML = tmpHTML.replace("(URL)", baseURL + "presentation/images/" + buttonItem.conf.icon);

			if( buttonItem.conf.styleClass != "" && (typeof buttonItem.conf.styleClass) == "string" ){
				tmpHTML = tmpHTML.replace("(CLASS)", " " + buttonItem.conf.styleClass);
			} else {
				tmpHTML = tmpHTML.replace("(CLASS)", "");
			}

			buttonItem.conf.HTML = tmpHTML;
			return buttonItem.conf.HTML;
		}
	
	}
	
	buttonItem.writeHTML();
	return buttonItem;

}