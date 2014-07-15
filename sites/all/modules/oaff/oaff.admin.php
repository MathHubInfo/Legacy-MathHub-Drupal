<?php

function oaff_admin_menu(& $items) {
  $items['mh/crawl-nodes'] = array(
    'title' => "Crawl Loaded Nodes",
    'page callback' => 'oaff_admin_crawl_nodes',
    'access callback' => 'oaff_admin_access',
    'menu_name' => 'navigation'
  );
  
  $items['mh/lmh-update'] = array(
  	'title' => "Lmh Update",
  	'page callback' => 'oaff_admin_lmh_update',
  	'access callback' => 'oaff_admin_access',
    'type' => MENU_CALLBACK,
  );
  
  $items['mh/libs-update'] = array(
  	'title' => "Update Libraries",
  	'page callback' => 'oaff_admin_libs_update',
  	'access callback' => 'oaff_admin_access',
    'type' => MENU_CALLBACK,
  );
  $items['mh/lmh-gen-omdoc'] = array(
  	'title' => "Lmh Generate OMDoc",
  	'page callback' => 'oaff_admin_lmh_gen_omdoc',
  	'access callback' => 'oaff_admin_access',
    'type' => MENU_CALLBACK,
  );
  $items['mh/mmt-rebuild'] = array(
  	'title' => "Rebuild MMT",
  	'page callback' => 'oaff_admin_mmt_rebuild',
  	'access callback' => 'oaff_admin_access',
    'type' => MENU_CALLBACK,
  );
  $items['mh/administrate_mathhub'] = array(
  	'title' => "Administer MathHub",
  	'page callback' => 'oaff_admin_administrate',
  	'access callback' => 'oaff_admin_access',
  	'menu_name' => 'navigation',
  );

  return $items;
}


/**
 * Implements access callback for OAFF auto-load feature
 * Only admin has access to reload mmt nodes
 */
function oaff_admin_access() {
  return user_access("administer mathhub");
}

/**
 * crawl the nodes already loaded in drupal to check for validity, and errors, and so on
 */
function oaff_admin_crawl_nodes() {
  $oaff_config = variable_get("oaff_config");
  if (isset($_GET['restart']) && $_GET['restart'] == 'true') {
    $oaff_config['crawl_nodes_offset'] = 0;
  }
  $offset = $oaff_config['crawl_nodes_offset'];
  $results = db_select('node', 'n')
              ->fields('n', array('nid'))
              ->condition('n.type', 'oaff_doc', '=')
              ->range($offset, 30)
              ->execute()
              ->fetchAll();

  foreach ($results as $result) {
    node_view(node_load($result->nid));
  }
  $crawled = count($results);
  $oaff_config['crawl_nodes_offset'] += $crawled;
  variable_set('oaff_config', $oaff_config);
  if ($crawled == 0) {
    if ($offset == 0) {
      drupal_set_message("Nothing to crawl (no nodes) (perhaps initialize nodes)");
    } else {
      drupal_set_message("Finished crawling nodes, no new nodes (perhaps restart)");
    }
  } else {
    drupal_set_message("Crawled $crawled nodes (with offset $offset)");
  }
  drupal_set_breadcrumb(array());
  $out = '<div> <button onclick="window.location = \'/mh/crawl-nodes?restart=true\'" class="btn btn-danger"> Restart </button>';
  $out .= ' <button class="btn btn-primary " onclick="window.location = \'/mh/crawl-nodes\'"> Continue </button> </div> ';
  return $out;
}

function oaff_admin_administrate() {
  $out  = '<h4> This page provides some admin-level functionality for MathHub.info </h4>';
  $out .= '<ul> <li> Get The latest version of the source documents ';
  $out .= '<button onclick="window.location = \'/mh/lmh-update\'" class="btn btn-primary btn-xs"> Lmh Update </button> </li>';
  $out .= '<li> Regenerate OMDoc (runs in background) ';
  $out .= '<button onclick="window.location = \'/mh/lmh-gen-omdoc\'" class="btn btn-warning btn-xs"> Lmh Generate OMDoc </button> </li>';
  $out .= '<li> Update Libraries (sTeX, MMT) ';
  $out .= '<button onclick="window.location = \'/mh/libs-update\'" class="btn btn-primary btn-xs"> Update Libs </button> </li>';
  $out .= '<li> Rebuild MMT archives';
  $out .= '<button onclick="window.location = \'/mh/mmt-rebuild\'" class="btn btn-primary btn-xs"> Rebuild MMT Archives </button> </li> </ul>';  

  //$out .= ' <button class="btn btn-primary " onclick="window.location = \'/mh/crawl-nodes\'"> Continue </button> </div> ';
  return $out;
}

function oaff_admin_lmh_update() {
	$lmh_status = shell_exec('lmh update --all 2>&1');
    oaff_log("OAFF.ADMIN", "`lmh update --all` returned: <pre>$lmh_status</pre>");
    drupal_set_message('Success');
    return '';
}

function oaff_admin_lmh_gen_omdoc() {
	$lmh_status = shell_exec('lmh gen --omdoc /var/data/localmh/MathHub/ >/dev/null 2 >/dev/null &');
	oaff_log("OAFF.ADMIN", "`lmh gen --omdoc /var/data/localmh/MathHub/` called in the background");
	drupal_set_message('Success');
	return '';
}

function oaff_admin_libs_update() {
	$git_log = shell_exec('cd /var/data/localmh/ext/sTeX/ && git pull 2>&1 && cd /var/data/localmh/ext/MMT/ && svn up 2>&1');
	oaff_log("OAFF.ADMIN", "`git pull` returned: <pre>$git_log</pre>");
	drupal_set_message('Success');
	return '';
}

function oaff_admin_mmt_rebuild() {
	$mmt_log = shell_exec('/var/data/localmh/ext/MMT/rebuild-mathhub.sh 2>&1');
	oaff_log("OAFF.ADMIN", "`rebuild-mathhub.sh` returned: <pre>$mmt_log</pre>");
	drupal_set_message('Success');
	return '';
}