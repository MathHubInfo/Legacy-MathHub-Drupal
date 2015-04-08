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
			var me = this;
			res['Go To Declaration'] = function() {planetary.navigate(uri);};
		}
		return res;
	},
    
};

JOBAD.modules.register(planetaryNavigationGlossary);
})(jQuery);
	