<?php

// returns information in tabs
// Parametres:
// data -- array of dictionaries with 2 fields:
//		'title'
//		'content'
function oaff_generate_tabs($data) {
	$out = "<div role=\"tabpanel\">";
	$out .= "	<ul class=\"nav nav-tabs\" role=\"tablist\">";
	// generate tab titles
	for ($i = 0; $i < sizeof($data); $i++) {
		$id = str_replace(" ", "_", strtolower($data[$i]['title']));
		// default tab
		if ($i == 0) {
			$out .= "<li role=\"presentation\" class=\"active\"><a href=\"#$id\" aria-controls=\"$id\" role=\"tab\" data-toggle=\"tab\">".$data[$i]['title']."</a></li>";
		} else {
			$out .= "<li role=\"presentation\"><a href=\"#$id\" aria-controls=\"$id\" role=\"tab\" data-toggle=\"tab\">".$data[$i]['title']."</a></li>";
		}
	}
	$out .= "	</ul>";
	// generate tab content
	$out .= "	<div class=\"tab-content\">";
	for ($i = 0; $i < sizeof($data); $i++) {
		$id = str_replace(" ", "_", strtolower($data[$i]['title']));
		if ($i == 0) {
			$out .= "<div role=\"tabpanel\" class=\"tab-pane active\" id=\"$id\">".$data[$i]['content']."</div>";
		} else {
			$out .= "<div role=\"tabpanel\" class=\"tab-pane\" id=\"$id\">".$data[$i]['content']."</div>";
		}	
	}
	$out .= "	</div>";
	$out .= "</div>";
	return $out;
}