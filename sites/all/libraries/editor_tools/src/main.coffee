define (require) ->
  
  $ = jQuery if not $?;
  Toolbar = require("editor_tools/toolbar");

  class EditorTools
    constructor : (@editor, id, config={}) ->
      @id = id;

      wrapped = $(id).wrap("<div>").parent();  
      toolbarDiv = $("<div>").attr("role", "toolbar").append($("<div>").addClass("toolbar_last"));
      wrapped.prepend(toolbarDiv);

      @toolbar = new Toolbar(toolbarDiv);

      ace.config.loadModule("ace/ext/language_tools", (tools) =>
        editor.setOptions({
          #enableSnippets: false,
          enableBasicAutocompletion: true
        })
      );

    getToolbar : () -> @toolbar;

