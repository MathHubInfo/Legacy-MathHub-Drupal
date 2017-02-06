/*************************************************************************
* This file is part of the MathHub.info System  (http://mathhub.info).   *
* It is hosted at https://github.com/KWARC/MathHub                       *
* Copyright (c) 2015 by the KWARC group (http://kwarc.info)              *
* Licensed under GPL3, see http://www.gnu.org/licenses/gpl.html          *
**************************************************************************/

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
        var lang = locale.getLanguage(target);
        var usedIn = locale.translate("Used In", lang);
        var uses = locale.translate("Uses", lang);
    	if (target.hasAttribute(mmtattr.symref)) {
			var uri = target.attr(mmtattr.symref);
			menu_entries[usedIn] = me.getRelated(uri, qmt.tosubject("Includes"));
			menu_entries[uses] = me.getRelated(uri, qmt.toobject("Includes"));
		}
		return menu_entries;
    },

    getRelated: function(uri, relation) {
        // TODO: 
        if (uri.match(/\?/g).length >= 2) {
            slices = uri.split('?');
            slices.pop();
            uri = slices.join('?');
        }
    	var query = qmt.related(qmt.literalPath(uri), relation);
    	var related_uris = [];
    	var me = this;
    	qmt.exec(query, 
    		 function(data) {
    		     $(data).find("uri").each(function (i, val) {
			 		
    					var path = $(val).attr('path');
			 console.log(path);
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

    // buils submenus recursively
    // uris -- array of arrays where first element is array
    //         of of splitted path by "/",
    //         second element is function to navigate
    // prefix -- prefix to add to each enry of the menu
    buildSubMenus: function(uris, prefix="") {
        if (uris.length  == 0) 
            return {};
        if (prefix != "") {
            prefix += "/";
        }
        var temp_uris = {};
        
        // sort uris in lexical order
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

        // if more then 10 entries separate alphabetically
        // in groups of 10 
        result = this.buildAlphaSubMenu(result);

        // in case there is only one entry in the menu
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

    // returns how many elements are in the object
    objectLength: function(obj) {
        var length = 0;
        for (var key in obj) {
            length++;
        }
        return length;
    },

    // separates object with more then 10 entries
    // into groups of ten alphabetically 
    buildAlphaSubMenu: function(uris, depth = 0) {
        if (this.objectLength(uris) <= 10)
            return uris;
        result = {};
        var count = 0;
        var name = "";
        var fletter = "";
        var temp_uris = {};
        for (var key in uris) {
            temp_uris[key] = uris[key];
            count++;
            fletter = key.split("/").slice(-1)[0].charAt(0).toLowerCase();
            if (count == 1) {
                name = fletter + " - ";
            }    
            else if (count == 10) {
                name += fletter;
                result[name] = temp_uris;
                temp_uris = {};
                count = 0;
            }
        }
        // if less then 10 elements left
        if (count != 0) {
            name += fletter;
            result[name] = temp_uris;
        }
        return result;
    }
};

JOBAD.modules.register(ontologyNavigation);
})(jQuery);

