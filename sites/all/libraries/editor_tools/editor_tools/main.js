// Generated by CoffeeScript 1.7.1
define(function(require) {
  var $, EditorTools, Toolbar;
  if (typeof $ === "undefined" || $ === null) {
    $ = jQuery;
  }
  Toolbar = require("editor_tools/toolbar");
  return EditorTools = (function() {
    function EditorTools(editor, id, config) {
      var toolbarDiv, wrapped;
      this.editor = editor;
      if (config == null) {
        config = {};
      }
      this.id = id;
      wrapped = $(id).wrap("<div>").parent();
      toolbarDiv = $("<div>").attr("role", "toolbar").append($("<div>").addClass("toolbar_last"));
      wrapped.prepend(toolbarDiv);
      this.toolbar = new Toolbar(toolbarDiv);
      ace.config.loadModule("ace/ext/language_tools", (function(_this) {
        return function(tools) {
          return editor.setOptions({
            enableBasicAutocompletion: true
          });
        };
      })(this));
    }

    EditorTools.prototype.getToolbar = function() {
      return this.toolbar;
    };

    return EditorTools;

  })();
});
