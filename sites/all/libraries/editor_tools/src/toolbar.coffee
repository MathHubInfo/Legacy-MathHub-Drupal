define (require) ->
  $ = jQuery if not $?;
  require("extlibs/jquery.tooltipster.min");

  class Toolbar
    constructor: (@top) ->
      @sections = {};
 
    addSection: (section) ->
      return @sections[section] if @sections[section]?;
      section_div = $("<div>").addClass("btn-group").attr("alt", section);
      after = "";
      for name of @sections
        if (name < section and name > after) 
          after = name;
      
      if after == ""
        @top.prepend(section_div);
      else
        @sections[after].after(section_div);
      @sections[section] = section_div;
      return section_div;

    removeSection: (section) ->
      return if section not in @sections;

    addItem: (id, icon, callback, text="", section="default") ->
      section = @addSection(section);
      img = $("<img>").attr("src", icon).attr("height", 16).attr("width", 16).attr("alt", text);
      btn = $("<button>").attr("type", "button").addClass("btn").addClass("btn-default").append(img).attr("id", id).attr("title", text);
      
      $(btn).click(callback);

      $(btn).tooltipster({content: text});
        
      $(section).append(btn);
      

    removeItem: (id) ->
      $("#"+id).remove();
