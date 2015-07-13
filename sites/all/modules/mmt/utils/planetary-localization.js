var locale = {
  /**
   * Dictionary used for translations
   * Stored as JS Object
   * Structure is EnglishString -> LangCode -> Translation (in that language)
   * Add entries here to improve localization across MathHub
   */
  dictionary: {
    "Show Definition" : {
      "ro" : "Vezi Definiția",
      "de" : "Definition Anzeigen",
      "zhs" : "显示定义",
      "zht" : "顯示定義",
    },
    "Go To Declaration" : {
      "ro" : "Deschide Declarația",
      "de" : "Gehe zu Deklaration",
      "zhs" : "前往详情",
      "zht" : "前往詳情",
    },
    "Used In" : {
      "zhs" : "使用于",
      "zht" : "使用於",
      "ro" : "Utilizat în",
      "de" : "Verwendet in", 
    },
    "Uses" : {
      "zhs" : "使用",
      "zht" : "使用",
      "ro" : "Depinde de",
      "de" : "Benutzt", 
    },
    "View Source" : {
      "zhs" : "查看源文件",
      "zht" : "查看源文件",
      "de" : "Quelle anzeigen", 
    },
    "Raw" : {
      "zhs" : "源文件",
      "zht" : "源文件",
      "de" : "Roh", 
    },
    "History" : {
      "zhs" : "历史",
      "zht" : "歷史",
      "de" : "Verlauf", 
    },
  },
  /**
   * This is the function JOBAD modules should call to translate a string from English to a language `lang` 
   * Defaults to the original english string is no translation is found
   */
  translate: function (string, lang) {
    var me = this;
    if (typeof me.dictionary[string] != "undefined" && typeof me.dictionary[string][lang] != "undefined") {
      return me.dictionary[string][lang];
    } else {
      return string; //default
    }
  },	
  
  //check if current page is glossary to handle specially if needed
  inGlossary: function(uri) {
  	return window.location.pathname == "/mh/glossary";
  },
  
  /*
   * get the language of the current page 
   * Currently only implemented for glossary
   * should check URI for .<lang>. component for normal nodes
   */
  getLanguage: function(target) {
  	default_lang = "en";
    if (locale.inGlossary() == true) {
  		var elem = jQuery(target).closest("div .tab-pane");
      if (typeof elem.attr("id") == 'undefined')
        return default_lang;
      else
        return elem.attr("id").substring(5); //removing `gtab_` 
  	} else { //TODO implement this part too default
  		if (typeof(language) != "undefined")
        return language;
      else
        return default_lang;
  	}
  },
};
