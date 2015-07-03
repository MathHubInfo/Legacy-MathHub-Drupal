(function($){

var planetaryGitlab = {
	info: {
		'identifier' : 'kwarc.mmt.planetary.gitlab',
		'title' : 'Gitlab Navigation Service in Planetary',
		'author': 'MMT developer team',
		'description' : 'The navigation service for connecting to Gitlab repositories from Planetary',
		'version' : '1.0',
		'dependencies' : [],
		'hasCleanNamespace': false
	},

	contextMenuEntries: function(target, JOBADInstance) {
    	var frags = oaff_node_rel_path.split(".");
    	frags[frags.length - 1] = "tex"; //settings extension
    	var tex_path = frags.join(".")
		var blob_url = 'http://gl.mathhub.info/' + oaff_node_group  + "/" + oaff_node_archive + "/blob/master/source/" + tex_path;
		var blame_url = 'http://gl.mathhub.info/' + oaff_node_group  + "/" + oaff_node_archive + "/blame/master/source/" + tex_path;
		var res = {
			'View Source':			
			{
				'Raw' : function() {window.open(blob_url, '_blank');},
				'History' : function() {window.open(blame_url, '_blank');},
			},
		};
		return res;
	},
};

JOBAD.modules.register(planetaryGitlab);
})(jQuery);