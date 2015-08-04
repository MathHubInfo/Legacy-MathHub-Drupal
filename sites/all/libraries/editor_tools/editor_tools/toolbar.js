// Generated by CoffeeScript 1.7.1
var __indexOf = [].indexOf || function(item) { for (var i = 0, l = this.length; i < l; i++) { if (i in this && this[i] === item) return i; } return -1; };

define(function(require) {
  var $, Toolbar;
  if (typeof $ === "undefined" || $ === null) {
    $ = jQuery;
  }
  require("extlibs/jquery.tooltipster.min");
  return Toolbar = (function() {
    function Toolbar(top) {
      this.top = top;
      this.sections = {};
    }

    Toolbar.prototype.addSection = function(section) {
      var after, name, section_div;
      if (this.sections[section] != null) {
        return this.sections[section];
      }
      section_div = $("<div>").addClass("btn-group").attr("alt", section);
      after = "";
      for (name in this.sections) {
        if (name < section && name > after) {
          after = name;
        }
      }
      if (after === "") {
        this.top.prepend(section_div);
      } else {
        this.sections[after].after(section_div);
      }
      this.sections[section] = section_div;
      return section_div;
    };

    Toolbar.prototype.removeSection = function(section) {
      if (__indexOf.call(this.sections, section) < 0) {

      }
    };

    Toolbar.prototype.addItem = function(id, icon, callback, text, section) {
      var btn, img;
      if (text == null) {
        text = "";
      }
      if (section == null) {
        section = "default";
      }
      section = this.addSection(section);
      img = $("<img>").attr("src", icon).attr("height", 16).attr("width", 16).attr("alt", text);
      btn = $("<button>").attr("type", "button").addClass("btn").addClass("btn-default").append(img).attr("id", id).attr("title", text);
      $(btn).click(callback);
      $(btn).tooltipster({
        content: text
      });
      return $(section).append(btn);
    };

    Toolbar.prototype.removeItem = function(id) {
      return $("#" + id).remove();
    };

    return Toolbar;

  })();
});
