<?php

function hook_permission() {
  return array(
    'administer mathhub' => array(
      'title' => t('Access to mathhub.info administration api'),
      'description' => t('Perform administration tasks for mathhub.info'),
    ),
  );
}

function oaff_log($component, $msg) {
	$trace = build_trace_msg(debug_backtrace());

	$oaff_config = variable_get("oaff_config");
	if ($oaff_config['logging'] == true) {
		if (user_access("administer mathhub")) {
			if (!isset($oaff_config['log'])) {
				$oaff_config['log'] = array();
			}
			$oaff_config['log'][] = array(
				'component' => $component,
				'msg' => $msg,
				'trace' => $trace);
			
			variable_set('oaff_config', $oaff_config);
		} //else no rights to see log => nothing to do
	} //else logging disabled =>  nothing to do
}

function oaff_log_produce_html($log) {
	$html =  '<div style="position:fixed;bottom:0;width:100%;background:white;" id="oaff_log">';
	foreach($log as $entry) {
		$component = $entry['component'];
		$msg = $entry['msg'];
		$trace = $entry['trace'];
		$html .= "<div> <h5><span class=\"text-info\"> $component: </span> <span class=\"bg-info\"> $msg </span></h5> $trace </div>";

	}
	$html .= '</div>';
	return $html;
}


function build_trace_msg($trace) {
	$func = $trace[1]['function'];
	$file = $trace[0]['file'];
	$line = $trace[0]['line'];
	$trace_msg = "<h4><small> from $func($file:$line)</small></h4>";
	return $trace_msg;
}


