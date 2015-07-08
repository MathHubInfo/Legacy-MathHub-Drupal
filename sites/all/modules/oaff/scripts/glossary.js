jQuery(function() {
jQuery(".gls_trigger").click(function() {
	var text = jQuery(this).attr("data-text");
	var textOn = "Show " + text;
	var textOff = "Hide " + text;
	var target = jQuery(this).attr("data-target");
	if (jQuery(this).html() == textOn) {
		jQuery(this).html(textOff);
	} else {
		jQuery(this).html(textOn);
	};

	jQuery(document.getElementById(target)).toggle('fold');
});


jQuery(".alt_lang").click(function() {
	var altId = jQuery(this).attr("data-id");
	var lang = jQuery(this).attr("data-lang");
	console.log(jQuery("#glossary a[data-target='#gtab_'" + lang + "']"));
	jQuery("#glossary a[data-target='#gtab_" + lang + "']").tab('show');
	window.location.href = "#" + altId;
	jQuery("#" + altId).parent().effect("highlight", {}, 1500);	
});

jQuery(".gs_tab").click(function() {
	jQuery(this).tab('show');
});

});