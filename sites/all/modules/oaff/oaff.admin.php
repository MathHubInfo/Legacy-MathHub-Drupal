<?php

function oaff_admin_menu(& $items) {
  $items['mh/crawl-nodes'] = array(
    'title' => "Crawl Loaded Nodes",
    'page callback' => 'oaff_admin_crawl_nodes',
    'access callback' => 'oaff_admin_access',
    'menu_name' => MENU_CALLBACK,
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
 * CRON  setup for periodically ran admin actions
 */
function oaff_cron_queue_info() {
  $queues['oaff_crawl_nodes'] = array(
    'worker callback' => 'oaff_admin_node_crawler',
    'time' => 60,
  );
  return $queues;
}

function oaff_cron() {
  $data = array();
  $queue = DrupalQueue::get('oaff_crawl_nodes');
  $queue->createItem($data);
}


function oaff_admin_node_crawler($arg = array()) {
  $oaff_config = variable_get('oaff_config');
  $offset = $oaff_config['crawl_nodes_offset'];
  $compiled_nodes = 0;
  $crawled = 0;
  $done = false;
  $needs_restart = false;
  while (!$done) {
    $results = db_select('node', 'n')
            ->fields('n', array('nid'))
            ->condition('n.type', 'oaff_doc', '=')
            ->range($offset + $crawled, 20)
            ->execute()
            ->fetchAll();
    foreach ($results as $result) {
      $node = node_load($result->nid);
      $location = $node->field_external['und']['0']['path'];
      $mtime = planetary_repo_stat_file($location)['mtime'];
      if (oaff_get_mtime($result->nid) != $mtime) { //file changed -> recompiling
        node_view($node);
        oaff_set_mtime($result->nid, $mtime);
        $compiled_nodes += 1;
      }
    }
    $crawled += count($results);
    if ($compiled_nodes >= 20 || $crawled >= 200 || count($results) == 0) { //compiled 20 or checked 100 or finished checking all nodes
      $done = true;
      if (count($results) == 0) { //finished checking all nodes
        $needs_restart = true;
      }
    }
  }
  if ($needs_restart) {
    $oaff_config['crawl_nodes_offset'] = 0; //we should re-start again
  } else {
    $oaff_config['crawl_nodes_offset'] = $offset + $crawled;
  }
  variable_set('oaff_config', $oaff_config);

  $result = array("crawled" => $crawled, "compiled" => $compiled_nodes);
  return $result;
}

/**
 * crawl the nodes already loaded in drupal to check for validity, and errors, and so on
 */
function oaff_admin_crawl_nodes() {
  $result = oaff_admin_node_crawler();
  $crawled = $result['crawled'];
  $compiled = $result['compiled'];
  if ($compiled == 0) {
    if ($crawled == 0) {
      drupal_set_message("Nothing to crawl (no nodes) (perhaps initialize nodes)");
    } else {
       drupal_set_message("Finished crawling nodes, no modified nodes to recompile ($crawled)");
    }
  } else {
    drupal_set_message("Crawled $crawled nodes, reran $compiled");
  }
  drupal_set_breadcrumb(array());
  $out = '<div> <button class="btn btn-primary " onclick="window.location = \'/mh/crawl-nodes\'"> Continue </button> </div> ';
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
  $out .= '<button onclick="window.location = \'/mh/mmt-rebuild\'" class="btn btn-primary btn-xs"> Rebuild MMT Archives </button> </li>';  
  $out .= '<li> Crawl Loaded Nodes';
  $out .= '<button onclick="window.location = \'/mh/crawl-nodes\'" class="btn btn-primary btn-xs"> Crawl Nodes </button> </li> </ul>';
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