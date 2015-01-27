(function($) {
			
function getDocumentID() {
  var m = document.URL.match("/node/([0-9]+)")
  if (m && m[1]) {
    return m[1];
  } else
  return Math.random();
}

function generateDocName(id, params) {
  if (params.field.match("edit-field-"))
    return "doc"+id+params.field; 
  else
    return "doc"+id+Math.random();
}

var envID = "planetary"+Math.random();

/**
 * Attach this editor to a target element.
 */
Drupal.wysiwyg.editor.attach.ace = function(context, params, settings) {
  // Attach editor.
    
  var editorID = "#"+params.field;
  var mode = "";
  var toolbardiv, editordiv, editorwrapper;
  cSettings = {
  	"mode" : "latex",
  	"ShareJS" : false,
  };
  
  if (typeof settings["enabled"]!="undefined") {
  	for (var i=0; i<settings["enabled"].length; ++i) {
  	  t = settings["enabled"][i].split("_");
  	  cSettings[t[0]]=t[1];
  	}
  }

  $(editorID).each(function (c, obj) {
  	jQuery(obj).hide();
        var editorid = "ace_"+params.field;
  	editordiv = jQuery("<div>").attr("id",editorid).attr("style"," height:400px; position:relative");
      
  	jQuery(obj).after(editordiv);
  	var editor = ace.edit(editorid);
        var fileName = jQuery(editordiv).parents(".fieldset-wrapper").children("input").val();
	  editor.getSession().setValue(obj.value);
	  editor.setTheme("ace/theme/textmate");
	  editor.getSession().setMode("ace/mode/"+cSettings["mode"]);

      require.config({ baseUrl: Drupal.settings.editor_tools.editor_tools_path,
            paths: {
                    "sally_client" : "extlibs/sally_client",
                    "EventEmitter" : "extlibs/EventEmitter.min",
                    "theo" : "extlibs/theo",
                    "frames" : "extlibs/frames",
                    "mathhubdocument" : "extlibs/mathhubdocument",
            }});

        require(["editor_tools/main", "sally_client", "theo", "frames", "mathhubdocument"], function(EditorTools, SallyClient, Theo, Frames, MathHubDocument) {
            var env = null;
            editor_tools = new EditorTools(editor, "#"+editorid);
	    
            var toolbar = editor_tools.getToolbar();

            var theo = new Theo();
            var frames = new Frames();
            var mathHubDocument = new MathHubDocument(editor, fileName, settings["sid"]);
            
            frames.on("NewDocLevelService", function(msg) {
              toolbar.addItem(msg.id, msg.icon, function() {
                frames.executeDocLevelService(msg.id);
              });
            });

            frames.on("RemoveDocLevelService", function(msg) {
              toolbar.removeItem(msg.id)
            });

            client = new SallyClient({stompUrl: settings["mh_url"], stompUser : settings["mh_user"], stompPassword : settings["mh_password"]});

            client.register([theo, frames, mathHubDocument], "env"+Math.floor(Math.random()*100000), function() {
              frames.listenDocLevelServices();
            });

        });

	  jQuery.data(obj, 'editor', editor);
  });
};

/**
 * Detach a single or all editors.
 *
 * See Drupal.wysiwyg.editor.detach.none() for a full desciption of this hook.
 */
Drupal.wysiwyg.editor.detach.ace = function(context, params) {
  if (typeof params != 'undefined') {
    var editorID = "#"+params.field;
    $(editorID).each(function (c, obj) {
    	var editor = jQuery.data(obj, 'editor');
    	if (editor != null) {
    		obj.value = editor.getSession().getValue()  
        	jQuery.data(obj, 'editor', null);
        	jQuery("#ace_"+params.field).remove();
        }
    	jQuery(obj).show();
    });
  }
};

})(jQuery);
