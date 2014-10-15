var planetary = {

  relNavigate: function(uri) {
  	var loc = window.location.pathname;
  	var locComps = loc.split("/");
	var uriMatches = uri.match(/^(http[s]?:\/\/)?([^:\/\s]+)(.*)$/);
	var uriComps = uriMatches[3].split("/");
	var last = uriComps[uriComps.length - 1];
	if (locComps[locComps.length - 1] == "") {
		locComps[locComps.length - 1] = last; // uri ends in slash => replace
	} else {
		locComps.push(last); //add uri comp
	}
	var path = locComps.join("/");
	window.location = path;
  },

  URIToURL: function(uri) {
	var matches = uri.match(/^(http[s]?:\/\/)?([^:\/\s]+)(.*)$/);
	var comps = matches[3].split("/");
	comps.splice(3,0,"source");
	var path = comps.join("/");
	return path;
  },

  navigate: function(uri) {
  	var path = URIToURL(uri);
  	window.location = path;
  },  
};