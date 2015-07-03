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
		if(target.hasAttribute(mmtattr.symref) && target.hasAttribute('data-relative')) {
			var uri = target.attr(mmtattr.symref);
			var uriEnc = planetary.relNavigate(uri);
		}

		var flag = target.hasAttribute(mmtattr.symref);
		if(target.parent().hasAttribute(mmtattr.symref)) {
			var url = planetary.URIToURL(target.parent().attr(mmtattr.symref));
			window.location = url;
		}
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
			res['Go To Declaration'] = function() {planetary.navigate(uri);};
			if (uri.match(/\?/g).length >= 2) {	
				res['Show Definition'] = function() {
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
	                  	
	          			$('<div>')
	          				.addClass('modal fade bs-example-modal-lg')
	          				.attr({
	          					'tabindex':'-1', 
	          					'role':"dialog"
	          				})
	      				.append(
	      					$('<div>')
	  						.addClass("modal-dialog modal-lg").css('max-width','80%')
	      					.append(
	      						$('<div>')
	      							.addClass("modal-content")
	      							.html(data)
	      					)
	      				).appendTo('body').modal();
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
	