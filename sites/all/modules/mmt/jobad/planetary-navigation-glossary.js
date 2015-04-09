(function($){

var planetaryNavigationGlossary = {
	info: {
		'identifier' : 'kwarc.mmt.planetary.navigation.glossary',
		'title' : 'MMT Navigation Service in Planetary Glossary',
		'author': 'MMT developer team',
		'description' : 'The navigation service for browsing the Glossary in Planetary',
		'version' : '1.0',
		'dependencies' : [],
		'hasCleanNamespace': false
	},
  

    leftClick: function(target, JOBADInstance) {
		if(target.hasAttribute('jobad:href') && target.hasAttribute('data-relative')) {
			var uri = target.attr("jobad:href");
			var uriEnc = planetary.relNavigate(uri);
		}
		return false;
    },


    contextMenuEntries: function(target, JOBADInstance) {
    	var res = {};

		if (target.hasAttribute('jobad:href')) {			
			var mr = $(target).closest('mrow');
			var select = (mr.length === 0) ? target : mr[0];
			mmt.setSelected(select);
			var uri = target.attr('jobad:href');
			var matches = uri.match(/^(http[s]?:\/\/)?([^:\/\s]+)(.*)$/);
			var comps = matches[3].split("/");
			var group = comps[1]; //0 is empty string since matches[3] starts with '/'
			var arch = comps[2];
			var relPath = comps.slice(3).join("/");
			var frags = relPath.split(".");
    	    frags[frags.length - 1] = "tex"; //settings extension
    	    var tex_path = frags.join(".");
		    var blob_url = 'https://gl.mathhub.info/' + group  + "/" + arch + "/blob/master/source/" + tex_path;
			var me = this;
			res['Go To Source'] =  function() {window.open(blob_url, '_blank');},
			res['Go To Declaration'] = function() {planetary.navigate(uri);};

		}
		return res;
	},
    
};

JOBAD.modules.register(planetaryNavigationGlossary);
})(jQuery);
	