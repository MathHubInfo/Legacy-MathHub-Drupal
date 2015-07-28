/*************************************************************************
* This file is part of the MathHub.info System  (http://mathhub.info).   *
* It is hosted at https://github.com/KWARC/MathHub                       *
* Copyright (c) 2015 by the KWARC group (http://kwarc.info)              *
* Licensed under GPL3, see http://www.gnu.org/licenses/gpl.html          *
**************************************************************************/

(function($){

var planetaryNavigation = {
	info: {
		'identifier' : 'kwarc.mmt.planetary.navigation',
		'title' : 'MMT Navigation Service in Planetary',
		'author': 'MMT developer team',
		'description' : 'The navigation service for browsing MMT repositories in Planetary',
		'version' : '1.0',
		'dependencies' : [],
		'hasCleanNamespace': false
	},
  

    leftClick: function(target, JOBADInstance) {
    	//navigate only for when data-relative is set (i.e. for virtdocs where links were relative)
    	//TODO: now after URI fix (removed /source/) this should be refactored (attr renamed)
		if(target.hasAttribute(mmtattr.symref) && target.hasAttribute('data-relative')) {
			var uri = target.attr(mmtattr.symref);
			var uriEnc = planetary.relNavigate(uri);
		}

		//disable this for now, context menu navigation should be enough
		/*var flag = target.hasAttribute(mmtattr.symref);
		if(target.parent().hasAttribute(mmtattr.symref)) {
			var url = planetary.URIToURL(target.parent().attr(mmtattr.symref));
			window.location = url;
		}
		*/
		return false;
    },

    contextMenuEntries: function(target, JOBADInstance) {
    	var res = {};
		if (target.hasAttribute(mmtattr.symref)) {			
			var mr = $(target).closest('mrow');
			var select = (mr.length === 0) ? target : mr[0];
			mmt.setSelected(select);
			var uri = target.attr(mmtattr.symref);
			var me = this;
			var lang = locale.getLanguage(target);
			var goDeclS = locale.translate("Go To Declaration", lang);
			var showDefS = locale.translate("Show Definition", lang);
			
			res[goDeclS] = function() {planetary.navigate(uri);};
			if (uri.match(/\?/g).length >= 2) {	
				res[showDefS] = function() {
					$.ajax({ 
					  'url': mmtUrl + "/:planetary/getRelated",
	   	  			  'type' : 'POST',
				      'data' : '{ "subject" : "' + uri + '",' + 
				      	'"relation" : "isDefinedBy",' + 
				        '"return" : "planetary"}',
				       'dataType' : 'html',
				       'processData' : 'false',
		       			'contentType' : 'text/plain',
		              'crossDomain': true,
	                  'success': function cont(data) {
	                  	var elem = $("#def_lookup_content");
	                  	if (elem.length == 0) { //not exists => creating modal 
		          			$('<div id="def_lookup_main">')
		          				.addClass('modal fade bs-example-modal-lg')
		          				.attr({
		          					'tabindex':'-1', 
		          					'role':"dialog"
		          				})
		      				.append(
		      					$('<div>')
		  						.addClass("modal-dialog modal-lg").css('max-width','80%')
		      					.append(
		      						$('<div id="def_lookup_content">')
		      							.addClass("modal-content")
		      							.html(data)
		      					)
		      				).appendTo('body').modal();
		      			} else { //just updating content
		      				elem.html(data);
		      				$("#def_lookup_main").modal();
		      			}
	                  },
	                  'error' : function( reqObj, status, error ) {
						console.log( "ERROR:", error, "\n ",status );
			    	  },
	                });
				};
			}
		}
		return res;
	},
    
};

JOBAD.modules.register(planetaryNavigation);
})(jQuery);
	