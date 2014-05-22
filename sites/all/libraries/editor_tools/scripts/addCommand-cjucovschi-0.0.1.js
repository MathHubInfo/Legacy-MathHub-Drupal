define(function(require) { return function(ace) { var core = require("scripts/core-cjucovschi-0.0.1"); core.setAce(ace); 

var cmdScript = jQuery("<div>").attr("id", "cmd-script").attr("style", "height: 400px; width: 100%");
var scriptFrame = jQuery("<div>");
scriptFrame.append("Name <input type='text'>");
scriptFrame.append(cmdScript);

jQuery(scriptFrame).dialog({
	width : "430px",
	heiht: "380px",
 	buttons : [
	{
	    text: "Insert",
	    click : function() {
		var math = jQuery(mathFrame).find(".mathquill-editable").mathquill('latex');
		core.insert(math);
		jQuery(mathFrame).dialog("close");
	    }
	}]
});

var editor = window.ace.edit("cmd-script");
editor.setTheme("ace/theme/textmate");
editor.getSession().setMode("ace/mode/javascript");

}});