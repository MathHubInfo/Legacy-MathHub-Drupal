(function($){

var ontologyNavigation = {
	info: {
		'identifier' : 'kwarc.mmt.ontology.navigation',
		'title' : 'MMT Navigation Service in based on ontology',
		'author': 'MMT developer team',
		'description' : 'A navigation service for browsing MMT repositories based on relational information ',
		'version' : '1.0',
		'dependencies' : [],
		'hasCleanNamespace': false
	},

    contextMenuEntries: function(target, JOBADInstance) {
    	var me = this;
    	var menu_entries = {};
    	if (target.hasAttribute(mmtattr.symref)) {
			var uri = target.attr(mmtattr.symref);
			menu_entries['Used In'] = me.getRelated(uri, qmt.tosubject("Includes"));
			menu_entries['Uses'] = me.getRelated(uri, qmt.toobject("Includes"));
		}
		return menu_entries;
    },

    getRelated: function(uri, relation) {
    	var query = qmt.related(qmt.literalPath(uri), relation);
    	var related_uris = [];
    	var me = this;
    	qmt.exec(query, 
    			 function(data) { 
    				$(data).find("uri").each(function (i, val) {
    					var path = $(val).attr('path');
    					related_uris.push([planetary.URIToURL(path).split("/").slice(1), function() {planetary.navigate(path);}]);
    				});
    			 },
    			 false);
        // disable if no data
        if (related_uris.length == 0)
    	   return false;
        else
           return me.buildSubMenus(related_uris);
    },

    buildSubMenus: function(uris, prefix="") {
        if (uris.length  == 0) 
            return {};
        if (prefix != "") {
            prefix += "/";
        }
        var temp_uris = {};
        var sorted = uris.sort(
            function(a, b){
                var a_ = a[0][0];
                var b_ = b[0][0];
                return a_ > b_;
            });
        var result = {};
        for (var i in sorted) {
            if (sorted[i][0].length == 1) {
                result[prefix + sorted[i][0][0]] = sorted[i][1];

            } else { 
                
                if (typeof temp_uris[sorted[i][0][0]] == 'undefined') {
                    temp_uris[sorted[i][0][0]] = [[sorted[i][0].slice(1),sorted[i][1]]];
                } else {
                    temp_uris[sorted[i][0][0]].push([sorted[i][0].slice(1),sorted[i][1]]);
                }
            }
        }

        result = this.buildAlphaSubMenu(result);

        if (this.objectLength(temp_uris) == 1) {
            for (var key in temp_uris) {
                jQuery.extend(result, this.buildSubMenus(temp_uris[key],prefix + key));
            }   
        } else {
            for (var key in temp_uris) {
                result[prefix + key] = this.buildSubMenus(temp_uris[key]);
            }
        }

        return result;
    },

    objectLength: function(obj) {
        var length = 0;
        for (var key in obj) {
            length++;
        }
        return length;
    },

    buildAlphaSubMenu: function(uris, depth = 0) {
        if (this.objectLength(uris) <= 10)
            return uris;
        result = {};
        var count = 0;
        var name = "";
        var temp_uris = {};
        for (var key in uris) {
            temp_uris[key] = uris[key];
            count++;
            if (count == 1) {
                name = key.split("/").slice(-1)[0].charAt(0) + " - ";
                console.log(key.split("/").slice(-1));
            }    
            else if (count == 10) {
                name += key.split("/").slice(-1)[0].charAt(0);
                result[name] = temp_uris;
                temp_uris = {};
                count = 0;
            }
        }
        return result;
    }
};

JOBAD.modules.register(ontologyNavigation);
})(jQuery);

