var locale = {
  /**
   * Dictionary used for translations
   * Stored as JS Object
   * Structure is EnglishString -> LangCode -> Translation (in that language)
   * Add entries here to improve localization across MathHub
   */
  dictionary: {
    "Show Definition" : {
      "ro" : "Vezi Definitia",
      "de" : "Definition Anzeigen",
    },
    "Go To Declaration" : {
      "ro" : "Deschide Declaratia",
      "de" : "Gehe zu Deklaration",
    }
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
  	if (locale.inGlossary() == true) {
  		var elem = jQuery(target).closest("div .tab-pane");
  		return elem.attr("id").substring(5); //removing `gtab_` 
  	} else { //TODO implement this part too default
  		return "en";
  	}
  },
};