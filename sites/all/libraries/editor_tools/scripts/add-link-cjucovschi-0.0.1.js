define(function(require) { return function(ace) { var core = require("scripts/core-cjucovschi-0.0.1"); core.setAce(ace); 

var $ = jQuery;

var resultWrapper = $("<div>");

var queue = core.getPrivateQueue();
var scriptFrame;
var scriptDiv;

  var id = core.registerCallback(function(msg) {
    var data = JSON.parse(msg.body);
    if (data.action == "select") {
      core.selectOffset(data.offset_begin, data.offset_end);
      return true;
    }

    if (data.action == "insert") {
      core.replaceOffset(data.offset_begin, data.offset_end, data.replaceString);
      return true;
    }
  });

  var issuePOST = function(to, p, target) {
    var myForm = document.createElement("form");
    myForm.method = "post";
    myForm.action = to;
    myForm.target = target;
    if (p) {
      for (var k in p) {
          var myInput = document.createElement("input");
          myInput.setAttribute("name", k);
          myInput.setAttribute("value", p[k]);
          myForm.appendChild(myInput);
        }
    }
    document.body.appendChild(myForm);
    myForm.submit();
    document.body.removeChild(myForm);
  };

  scriptFrame = $("<iframe>").attr("style", "width:100%; height: 100%").attr("id", "add-link-service").attr("name", "add-link-service");

  scriptDiv = $("<div>").append(scriptFrame);

  $(scriptDiv).dialog({
      height: 340,
      width: 450,
      title: "Add Links",
      beforeClose: function( event, ui ) {
        $(scriptFrame).remove();        
      }
  });

  scriptDiv.addClass("no-margins");

  issuePOST("http://mathhub.info:8983/sider-nnexus/app/link", {"forward_destination": queue, "forward_correlation":id, body: core.getText()}, "add-link-service");
//  issuePOST("http://localhost:8080/sider-nnexus/app/link", {"forward_destination": queue, "forward_correlation":id, body: core.getText()}, "add-link-service");

}});