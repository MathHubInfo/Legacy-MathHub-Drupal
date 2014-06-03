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
    $html = '<div style="z-index:1000;position:fixed;bottom:0;width:50%;left:25%;max-height:75%;overflow:auto;" class="panel-group" id="mh_dev_log_accordion">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <button class="btn btn-info" 
        onclick="jQuery(\'#mh_dev_log_collapse\').toggle();jQuery(\'#mh_log_toggle_btn\').toggleClass(\'glyphicon-plus-sign glyphicon-minus-sign\')" 
        href="#mh_dev_log_collapse">
              <span id="mh_log_toggle_btn" class="glyphicon glyphicon-plus-sign"></span>
        </button>
        <span> MathHub.info developer log </span>
      </h4>
    </div>
    <div id="mh_dev_log_collapse" style="display: none;">
      <div class="panel-body" style="background:lightgoldenrodyellow;" >';

	$html .=  '<div id="oaff_log">';
	foreach($log as $entry) {
		$component = $entry['component'];
		$msg = $entry['msg'];
		$trace = $entry['trace'];
		$html .= "<div> <h5><span class=\"text-info\"> $component: </span> <span class=\"bg-info\"> $msg </span></h5> $trace </div>";
	}
	$html .= '</div></div></div></div></div>';
	return $html;
}

function oaff_page_build(& $page) {
	if (user_access("administer mathhub")) {
	    $oaff_config = variable_get('oaff_config');
	    $log = $oaff_config['log'];
	    if (count($log) > 0) {
	        $oaff_config['log'] = array();
	        variable_set('oaff_config', $oaff_config);
		    if (!isset($page['content'])) {
		    	$page['content'] = array();
		    }
		    if (!isset($page['content']['system_main'])) {
		    	 $page['content']['system_main'] = array();
		    }
		    if (!isset($page['content']['system_main']['main'])) {
				$page['content']['system_main']['main'] = array();
			 	$page['content']['system_main']['main']['#markup'] = '';
			}
			$page['content']['system_main']['main']['#markup'] .= oaff_log_produce_html($log);
	    }
	}
}

function build_trace_msg($trace) {
	$func = $trace[1]['function'];
	$file = $trace[0]['file'];
	$line = $trace[0]['line'];
	$trace_msg = "<h4><small> from $func ($file:$line)</small></h4>";
	return $trace_msg;
}