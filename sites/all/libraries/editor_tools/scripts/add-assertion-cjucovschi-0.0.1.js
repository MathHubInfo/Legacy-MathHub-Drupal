define(function(require) { return function(ace) { var core = require("scripts/core-cjucovschi-0.0.1"); core.setAce(ace); 

var $ = jQuery;

var resultWrapper = $("<div>");

var queue = core.getPrivateQueue();
var scriptFrame;
var scriptDiv;

  var id = core.registerCallback(function(msg) {
    data = JSON.parse(msg.body);
    core.insert(data.tex);
    $(scriptDiv).dialog("close");
  });

  scriptFrame = $("<iframe>").attr("src", "http://mathhub.info:8983/stex-wizards/app/assertion?forward_destination="+queue+"&forward_correlation="+id).attr("style", "width:100%; height: 100%");
  scriptDiv = $("<div>").append(scriptFrame);

  $(scriptDiv).dialog({
      height: 540,
      width: 1050,
      title: "Add assertion"
  });

  scriptDiv.addClass("no-margins");

}});