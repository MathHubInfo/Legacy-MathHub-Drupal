define (require) ->

	$ = jQuery if not $?;
	Toolbar = require "editor_tools/scriptable_toolbar"
	Interpretter = require "editor_tools/interpretter"
	SallyClient = require "editor_tools/sally_client"

	planetaryNS = "http://kwarc.info/sally/comm/planetaryclient";
	textNS = "http://kwarc.info/sally/comm/planetaryclient";

	enrich_editor : (@editor, id, config={}) ->
		@id = id;
		config = $.extend({
			root_path : "",
			stompUrl : "ws://mathhub.info:61614", 
			stompUser : "webclient", 
			stompPassword : "webclient",
			envid : "random_edit"+Math.random();
			servletAddress : "http://mathhub.info:8181",
			sid : ""
		}, config);

		handler = (body, msg, response) ->
			if body["GetDocumentMeta"]? and body["GetDocumentMeta"]["@xmlns"] == planetaryNS
				response({"GetDocumentMetaResponse" : {"@xmlns" : planetaryNS, "sessionid" : config.sid, "filepath" : config.file}})
			if body.NewService?
				interpretter.addImplementation(body.NewService.id, () ->
					dv = $("<div>").append($("<iframe>").attr("src", body.NewService.url).attr("style", "width:100%;height:auto"));
					$(dv).dialog();
				)
				homeMenu = toolbar.addMenu("Home");
				MHWSection = toolbar.addSection(homeMenu, "MathHub services");
				toolbar.addItem(MHWSection, body.NewService.id, body.NewService.icon);
			if body.RemoveService?
				interpretter.removeImplementation(body.RemoveService.id)
				homeMenu = toolbar.addMenu("Home");
				MHWSection = toolbar.addSection(homeMenu, "MathHub services");
				toolbar.removeItem(MHWSection, body.RemoveService.id);


		sallyclient = new SallyClient(config, handler)
		editor.sallyclient = sallyclient;

		ace.config.loadModule("ace/ext/language_tools", (tools) =>
			editor.setOptions({
				#enableSnippets: true,
				enableBasicAutocompletion: true
			});
			tools.addCompleter({
				getCompletions : (editor, session, pos, prefix, callback) =>
					pos = editor.getCursorPosition()

					responseCallback = (_msg) ->
						_msg = _msg.AutocompleteResponse;
						if (not _msg? or not _msg.suggestion?)
							return true;
						if not _msg.hasOwnProperty("length") # just one result
							msg = [ _msg.suggestion ];
						else
							msg = _msg.suggestion;
						res = msg.map((suggestion) ->
								trimmedConcept = suggestion.concept
								trimLen = 30
								if trimmedConcept.length > trimLen
									trimmedConcept = "..."+trimmedConcept.substr(trimmedConcept.length-trimLen, trimLen);
								return {
									name: suggestion.text,
									value: suggestion.text,
									caption: trimmedConcept
									completer :
										insertMatch : (editor) ->
											editor.execCommand("insertstring", "\\trefi{"+suggestion.text+"}");
									meta: "remote"
								});
						callback(null, res);
						return true
						

					sallyclient.sendSally(
						{"AutocompleteRequest" : 
							"@xmlns" : textNS, 
							"text": editor.getValue(),
							"line" : pos.row,
							"col" : pos.column,
							"path" : config.file,
							"prefix" : prefix
						}, responseCallback);
				})
			);

		wrapped = $(id).wrap("<div>").parent();
		$(id).addClass("ui-layout-center");
		header = $("<div>").addClass("ui-layout-north");
		terminal = $("<div>").addClass("ui-layout-south");
		wrapped.prepend(header);
		wrapped.append(terminal);
		layout = null

#		jQuery(document).ready(() ->
#			$(wrapped).width($(id).width())
#			$(wrapped).height($(id).height())
#
#			layout = wrapped.layout({ 
#				enableCursorHotkey : false,
#				north : {
#					closable : false,
#					size : 120,
#					resizable : false,
#				},
#				south : {
#					resizable : true,
#					size: 80,
#					initClosed: true,
#				}
#			});
#		)

		interpretter = new Interpretter(@editor);
		toolbar = new Toolbar(header, interpretter, config.root_path);

		sallyclient.connect ["planetaryclient", "theo"], config.envid, ()=>
			console.log("connected")

#		termToggle = (evt)->
#			# if C+` was pressed
#			if evt.keyCode == 192 && evt.ctrlKey
#				if layout.state.south.isClosed
#					layout.open("south");
#					$(terminal).trigger("click");    
#					$(terminal).focus();
#					editor.blur();
#				else
#					layout.close("south");
#					editor.focus();
#				return false
#
#		$(terminal).terminal((command, term) ->
#			term.echo(interpretter.exec(command));
#		,{
#			greetings: "",
#			tabcompletion : true,
#			keyDown: termToggle,
#			completion : (terminal, string, callback) -> interpretter.autocomplete(string, callback);
#		});


#		$(wrapped).keydown termToggle
#
		{
			toolbar : toolbar,
			interpretter : interpretter,
			editor : @editor,
			header: header,
		}