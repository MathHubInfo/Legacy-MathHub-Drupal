jQuery(function() {
jQuery(".gls_trigger").click(function() {
	var target = jQuery(this).attr("data-target");
	jQuery(document.getElementById(target)).toggle('fold');
});


jQuery(".alt_lang").click(function() {
	var altId = jQuery(this).attr("data-id");
	var lang = jQuery(this).attr("data-lang");
	jQuery("#glossary a[data-target='#gtab_" + lang + "']").tab('show');
	window.location.href = "#" + altId;
	jQuery("#" + altId).parent().effect("highlight", {}, 1500);	
});

jQuery(".gs_tab").click(function() {
	jQuery(this).tab('show');
});

});