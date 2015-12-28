<?php

/*************************************************************************
* This file is part of the MathHub.info System  (http://mathhub.info).   *
* It is hosted at https://github.com/KWARC/MathHub                       *
* Copyright (c) 2015 by the KWARC group (http://kwarc.info)              *
* Licensed under GPL3, see http://www.gnu.org/licenses/gpl.html          *
**************************************************************************/

function oaff_admin_menu(& $items) {
  $items['mh/crawl-nodes'] = array(
    'title' => "Crawl Loaded Nodes",
    'page callback' => 'oaff_admin_crawl_nodes',
    'access callback' => 'oaff_admin_access',
    'menu_name' => MENU_CALLBACK,
  );
  $items['mh/touch-files'] = array(
    'title' => "Touch Source File",
    'page callback' => 'oaff_admin_touch_files',
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
  $items['mh/generate-glossary'] = array(
    'title' => "Regenerate Glossary",
    'page callback' => 'oaff_admin_generate_glossary',
    'access callback' => 'oaff_admin_access',
    'type' => MENU_CALLBACK,
  );
  $items['mh/rebuild-libs'] = array(
    'title' => "Rebuild Libraries",
    'page callback' => 'oaff_admin_rebuild_libs',
    'access callback' => 'oaff_admin_access',
    'type' => MENU_CALLBACK,
  );
  $items['mh/administrate_mathhub'] = array(
    'title' => "Admin",
    'page callback' => 'oaff_admin_administrate',
    'access callback' => 'oaff_admin_access',
    'menu_name' => 'main-menu',
    'weight' => 50,
  );  
  $items['mh/update_errors'] = array(
    'title' => "Update Errors",
    'page callback' => 'oaff_admin_update_errors',
    'access callback' => true, //needs public for providing builder API?
    'menu_name' => MENU_CALLBACK,
  );
  $items['mh/mbt-rebuild'] = array(
    'title' => "MBT Update Build",
    'page callback' => 'oaff_admin_mbt_rebuild',
    'access callback' => 'oaff_admin_access', //needs public for providing builder API?
    'menu_name' => MENU_CALLBACK,
  );
  return $items;
}

function oaff_admin_update_errors() {
  if (isset($_POST["path"])) {
    $location = $_POST["path"];
    $path_info = oaff_base_get_path_info($location);
    $alias = $path_info['alias'];
    $path = drupal_lookup_path("source", $alias);
    $node = menu_get_object("node", 1, $path);
    node_view($node);
    drupal_set_message("Success");
    return " ";
  } else {
    drupal_set_message("Failure, no field 'path' in post request");
    return " ";
  }
}

// touch one file or all files in the group
function oaff_admin_touch_files() {
  $html="";
  if (!isset($_GET['act'])) {
    $html = '
    <div class="row">
      <div class="col-lg-12">
        <div class="panel panel-default" id="fileHeading">
           <div class="panel-heading" role="tab">
              <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion" href="#fileBody" aria-expanded="false" aria-controls="filterBody" class="">
                  Files
                </a>
              </h4>
            </div>
        <div id="fileBody" class="panel-collapse in" role="tabpanel" aria-labelledby="fileHeading" style="height: auto;">
          <div class="panel-body">
            <div class="form-group">
              <label> Group </label>
              <input id="mh_group" type="text" class="form-control" name="group" placeholder="Enter group name">
              <p class="help-block">Leave this field empty to touch all files.</p>
            </div>
            <div class="form-group">
              <label> Archive </label>
              <input id="mh_archive" type="text" class="form-control" name="archive" placeholder="Enter archive name">
              <p class="help-block">Leave this field empty to touch all files in all archives.</p>
            </div>
            <div class="form-group">
              <label> File </label>
              <input id="mh_fname" type="text" class="form-control" name="fname" placeholder="Enter file name">
              <p class="help-block">Leave this field empty to touch all files in the archive.</p>
            </div>
            <button onclick="touchFiles()" type="button" class="btn btn-danger" action>Submit</button>  
          </div>
        </div>
        </div>

        <div class="panel panel-default" id="regexHeading">
           <div class="panel-heading" role="tab">
              <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion" href="#regexBody" aria-expanded="false" aria-controls="regexBody" class="">
                  Regular expression
                </a>
              </h4>
            </div>
        <div id="regexBody" class="panel-collapse in" role="tabpanel" aria-labelledby="regexHeading" style="height: auto;">
          <div class="panel-body">
            <div class="form-group">
              <label> Regular expression </label>
              <input id="mh_regex" type="text" class="form-control" name="regex" placeholder="Enter regular expression">
              <p class="help-block">Leave this field empty in case of not using</p>
            </div>
            <button onclick="touchFilesRegex()" type="button" class="btn btn-danger" action>Submit</button>  
          </div>
        </div>
        </div>
      </div><!-- /.col-lg-6 -->
    </div><!-- /.row -->' ;
    drupal_add_js('
    function touchFiles() {
      var path = "/mh/touch-files?";
      var group = jQuery("#mh_group").get(0).value;
      var archive = jQuery("#mh_archive").get(0).value;
      var fname = jQuery("#mh_fname").get(0).value;
      path += "group=" + group + "&";
      path += "archive=" + archive + "&";
      path += "fname=" + fname + "&";
      path += "act=true";
      path = path.substring(0, path.length -1);
      window.location = path;
    }

    function touchFilesRegex() {
      var path = "/mh/touch-files?";
      var regex = encodeURI(jQuery("#mh_regex").get(0).value);
      path += "regex=" + regex + "&";
      path += "act=true";
      path = path.substring(0, path.length -1);
      window.location = path;
    }
      ', 'inline');

    //group, archive, filename
  } else if (!isset($_GET['regex'])){
    $group = $_GET['group'];
    if ($group == "") {
      $group = "*"; //default
    }

    $archive = $_GET['archive'];
    if ($archive == "") {
      $archive = "*"; //default
    }

    $fname = $_GET['fname']; 
    if ($fname == "") { 
      $fname = "*"; //default
    }
    
    $command = "find /var/data/localmh/MathHub/$group/$archive/source/$fname | xargs touch";
    shell_exec($command);
    drupal_set_message("Success");
    oaff_log("OAFF.ADMIN", "Ran $command");
    //regex
  } else {
      $regex = rawurldecode($_GET['regex']);
      $command = "cd /var/data/localmh/MathHub/; grep -H -R '$regex' * | cut -d: -f1 | xargs touch";
      shell_exec($command);
      drupal_set_message("Success");
      oaff_log("OAFF.ADMIN", "Ran $command");
  }
  return $html;
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
    'time' => 260,
  );
  return $queues;
}

function oaff_cron() {
  $data = array();
  $queue = DrupalQueue::get('oaff_crawl_nodes');
  $queue->createItem($data);
  $queue->createItem($data);
  $queue->createItem($data);
  $queue->createItem($data);
}

function oaff_admin_node_crawler($nids = array()) {
  $oaff_config = variable_get('oaff_config');
  $offset = $oaff_config['crawl_nodes_offset'];
  $compiled_nodes = 0;
  $crawled = 0;
  $done = false;
  $needs_restart = false;
  $max_compiled = 20;
  $max_crawled = 200;
  while (!$done) {
    $query = db_select('node', 'n');
    $query->join('field_data_field_external', 'p', 'n.nid = p.entity_id');
    $results = $query->fields('n', array('nid'))
            ->fields('p', array('field_external_path'))
            ->condition('n.type', 'oaff_doc', '=')
            ->range($offset + $crawled, 5)
            ->execute()
            ->fetchAll();
    foreach ($results as $result) {
      // $node = node_load($result->nid);
      // $location = $node->field_external['und']['0']['path'];
      $location = $result->field_external_path;
      $pathinfo = oaff_base_get_path_info($location);
      $mtime = planetary_repo_stat_file($location)['mtime'];
      $to_rerun = false;
      $mtimes = oaff_get_mtimes($result->nid);
      if (count($mtimes) == 0) { // node not yet ran
        $to_rerun = true;
      }
      foreach ($mtimes as $mtime_entry) {
        $log_file = $mtime_entry->logfile;
        $time = $mtime_entry->mtime;
        $stat = planetary_repo_stat_file($log_file);
        if ($stat) {
          if ($time != $stat['mtime']) { //log changed
            $to_rerun = true; // mark for rerun
          }
        } else {
          drupal_set_message("Could not find log file for " + $log_file, 'warning');
        }
      }

      if ($to_rerun) {
        node_view(node_load($result->nid)); //this re-reads the logs where needed
        $compiled_nodes += 1;
      }
    }
    $crawled += count($results);
    if ($compiled_nodes >= $max_compiled || $crawled >= $max_crawled || count($results) == 0) { //compiled 20 or checked 100 or finished checking all nodes
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
  drupal_set_message("Crawl-Nodes Task Done");
  $result = array("crawled" => $crawled, "compiled" => $compiled_nodes, "offset" => $offset);
  return $result;
}

/**
 * crawl the nodes already loaded in drupal to check for validity, and errors, and so on
 */
function oaff_admin_crawl_nodes() {
  $result = oaff_admin_node_crawler();
  $crawled = $result['crawled'];
  $compiled = $result['compiled'];
  $offset = $result['offset'];
  if ($compiled == 0) {
    if ($crawled == 0 && $offset == 0) {
      drupal_set_message("Nothing to crawl (no nodes) (perhaps initialize nodes)");
    } else {
       drupal_set_message("Checked $crawled source files, no modified nodes to recompile ");
    }
  } else {
    drupal_set_message("Checked $crawled nodes, reran $compiled (offset $offset)");
  }
  drupal_set_breadcrumb(array());
  $out = '<div> <button class="btn btn-primary " onclick="window.location = \'/mh/crawl-nodes\'"> Continue </button> ';
  $out .= ' <button class="btn btn-danger" title="keeps reloading this tab" onclick="window.location = \'/mh/crawl-nodes?auto=true\'"> Auto-Pilot </button> </div> ';
  if (isset($_GET['auto'])) {
    drupal_set_message("Running on auto-pilot", 'warning');
    drupal_add_js("window.onload = function() {console.log('now'); window.location.reload('true');}", "inline");
  }
  return $out;
}

function oaff_admin_administrate() {
  $out  = '<h4>This page collects functionalities related to MathHub administration and maintenance</h4>';
  $out .= '<table class="table"><tbody>';
  $out .= '<tr><td><button onclick="window.location = \'/mh/lmh-update\'" class="btn btn-primary btn-xs"> Lmh Update </button></td>';
  $out .= '<td>Get the latest version of the source documents</td></tr>';
  $out .= '<tr><td><button onclick="window.location = \'/mh/libs-update\'" class="btn btn-primary btn-xs"> Update Libs </button></td>';
  $out .= '<td>Update Libraries (sTeX, MMT)</td></tr>';
  $out .= '<tr><td><button onclick="window.location = \'/mh/touch-files\'" class="btn btn-primary btn-xs"> Touch Files </button></td>';
  $out .= '<td>Touch Source Files (useful in case of compiler update to mark them as modified for crawler)</td></tr>';
  $out .= '<tr><td><button onclick="window.location = \'/mh/synchronize-nodes\'" class="btn btn-primary btn-xs"> Synchronize Nodes </button></td>';
  $out .= '<td>Synchronize with disk (create/delete nodes for new/removed files)</td></tr>';
  $out .= '<tr><td><button onclick="window.location = \'/mh/sync-nodes\'" class="btn btn-primary btn-xs"> Sync Nodes </button></td>';
  $out .= '<td>Synchronize with disk (create/delete nodes for new/removed files)</td></tr>';
  $out .= '<tr><td><button onclick="window.location = \'/mh/crawl-nodes\'" class="btn btn-primary btn-xs"> Crawl Nodes </button></td>';
  $out .= '<td>Crawl to update status info (errors logs) for nodes</td></tr>';
  $out .= '<tr><td><button onclick="window.location = \'/mh/generate-glossary\'" class="btn btn-primary btn-xs"> Regenerate </button></td>';
  $out .= '<td>Regenerate Glossary</td></tr>';
  $out .= '<tr><td><button onclick="window.location = \'/mh/mbt-rebuild\'" class="btn btn-primary btn-xs"> MBT Build </button></td>';
  $out .= '<td>Go to the MBT (Scala-based) build page</td></tr>';
  $out .= '<tr><td><p><button onclick="window.location = \'/mh/rebuild-libs\'" class="btn btn-primary btn-xs"> See Build Log </button></p>';
  $out .= '<p><button onclick="window.location = \'/mh/rebuild-libs?action=update-build\'" class="btn btn-warning btn-xs"> Update Build </button></p>';
  $out .= '<p><button onclick="window.location = \'/mh/rebuild-libs?action=clean-build\'" class="btn btn-warning btn-xs"> Clean Build </button></p></td>';
  $out .= '<td>Rebuild Everything';
  $lock = oaff_admin_get_build_lock_path();
  if (!$lock) {
    $out .= '<span class="alert-danger">(Currently running) </span>';
  }
  $out .= '</td></tr>';
  $out .= '</tbody></table>';
  //$out .= ' <button class="btn btn-primary " onclick="window.location = \'/mh/crawl-nodes\'"> Continue </button> </div> ';
  return $out;
}

//rebuilds everything
function oaff_admin_rebuild_libs() {
  $out = '';
  $action = ''; //default
  if (isset($_GET['action'])) {
    $action = $_GET['action'];
  }
  $mh_base = "/var/data/localmh/MathHub";
  $lock = oaff_admin_get_build_lock_path();
  $base = '/var/data/localmh/MathHub/meta/inf/config/MathHub/';
  if ($action == "") {
    if (!$lock) {
      drupal_set_message("Currently a build is running");
    }
  } else if ($action == "clean-build") {
    if ($lock) {
      exec($base . 'clean-build.sh > /dev/null 2>&1 &');
      drupal_set_message("Started (clean) build process");
    } else {
      drupal_set_message("Did not start rebuild, a build process is already running (lock is set)", "warning");
    }
  } else if ($action == "update-build") {
    if ($lock) {
      exec($base . 'update-build.sh > /dev/null 2>&1 &');
      drupal_set_message("Started (update) build process");
    } else {
      drupal_set_message("Did not start rebuild, a build process is already running (lock is set)", "warning");
    }    
  } else {
    drupal_set_message("Unknown action $action", "warning");
  }
  $rel_log_file = "meta/inf/config/MathHub/build.log";
  if (planetary_repo_stat_file($rel_log_file)) {// log exists
    $log = planetary_repo_load_file($rel_log_file);
    $out .= "<h4> See current build log below: </h4>";
    $out .= '<pre >' . check_plain($log) . '</pre>';
  } else {
    drupal_set_message("No log exists, perhaps no build happened", "error");
  }
  return $out;
}

//rebuilds only given paths, used by various features
function oaff_admin_nodes_rebuild($paths) {
  $lock = oaff_admin_get_build_lock_path();
  if ($lock) {
    $script  = "#!/bin/bash\n";
    $script .= "dir=`dirname $0`\n";
    $script .= "touch \$dir/build.lock\n";
    $script .= "echo 'getting lock' > \$dir/build.log\n";
    $script .= "lmh gen --sms --localpaths --all >> \$dir/build.log\n";
    foreach ($paths as $path) {
      $script .= "lmh gen --omdoc -f " . $path . " >> \$dir/build.log\n";
    }
    $script .= "\$dir/mmt-mh.sh update-build.msl >> \$dir/build.log\n";
    $script .= "rm -rf \$dir/build.lock\n";
    $script .= "echo 'finished, removing lock' >> \$dir/build.log\n";
    $rel_script_file = '/meta/inf/config/MathHub/build.tmp.sh';
    $script_file = planetary_repo_access_rel_path($rel_script_file);
    planetary_repo_save_file("/meta/inf/config/MathHub/build.tmp.sh", $script);
    exec("chmod +x " . $script_file);
    exec($script_file);
    exec("rm -rf " . $script_file);
    return true;
  } else {
    drupal_set_message("Did not start rebuild, a build process is already running (lock is set)", "error");
    return false;
  }
}

function oaff_admin_mbt_rebuild() {
  print_r($_GET);
  if (isset($_GET['act'])) {
    print_r("acting");
  }
  $oaff_config = variable_get('oaff_config');
  print_r($oaff_config);
  $form = '<form class="col-md-10">';
  //adding modifier choice
  $modifiers = array("Update" => "Rebuild all new/changed files", "Build" => "Rebuild all files", "Clean" => "Clean generated files for selected target(s)");
  $form .= '<div class="form-group">';
  $form .= '<h4> Build Modifier <span class="small" style="color:gray"> Select <i>how</i> to build.</span></h4>  ';
  foreach ($modifiers as $mod => $desc) {
    $form .= '<div class="col-md-4">';
    $form .= '<label class="radio-inline"> <input type="radio" name="modifier" value="'. $mod . '"> '. $mod .' </label>';
    $form .= '<p class="help-block">' . $desc .'</p>';  
    $form .= '</div>';
  } 
  $form .= '</div><hr class="col-md-12">';
  //adding compiler choice
  $compilers = array("latexml", "stex-omdoc", "mmt-omdoc", "planetary", "svg");
  $form .= '<div class="form-group">';
  $form .= '<h4>Build targets <span class="small" style="color:gray">Select which (if any) compilers changed </span></h4>';
  foreach ($compilers as $comp) {
    $form .='<label class="checkbox-inline"><input type="checkbox" name="compilers" value="'. $comp . '"> '. $comp .'</label>';
  }
  $form .= '<span class="help-block">Will rebuild selected target as well as dependent ones. If none are selected will rebuild everything </span>';  
  $form .= '</div><hr class="col-md-12">';
  //adding archive choice
  $form .= '<div class="form-group">';
  $form .= '<h4> Profiles <span class="small" style="color:gray"> Select which archives to build </span></h4>';
  //$profiles = $oaff_config['profiles'];
  $base_profiles = array("test" => array("smglom/mv"),"test2" => array("smglom/sets","smglom/mv"),"test3" => array("smglom/sets"),"test4" => array("smglom/sets"),"test5" => array("smglom/sets"));
  
  $profiles = array('All' => "Contains all active archives");
  foreach ($base_profiles as $prof => $archs) {
    $profiles[$prof] = "Contains " . implode(',', $archs);
  }
  foreach ($profiles as $profile => $desc) {
    $form .= '<div class="col-md-4">';
    $form .= '<label class="radio-inline"> <input type="radio" name="profile"> ' . $profile . '</label>';
    $form .= '<p class="help-block">' . $desc . '</p>';  
    $form .= '</div>';
  }
  $form .= '</div><hr class="col-md-12">';

  $form .= '
    <div class="col-md-12"> 
    <input type="hidden" name="act"/>
    <input class="btn btn-primary" type="submit" value="Submit">
    </div>';
  $form .= '</form>';

  return $form;

}

function oaff_admin_make_mbt($conf) {
  $mhBase = "/var/data/localmh/MathHub/";
  $script .= 'loadConfig("/var/data/localmh/MathHub/test-config.mcf")';
  $mod = "Build"; // default
  if (isset($conf["modifier"])) {
    $mod = $conf["modifier"];
  }
  if (isset($conf["target"])) {//smart build from target 
    $target = $conf["target"];
    $script .= 'compUpdateBuild(List("' .  $target . '"),' . $modifier . ')';
  } else {
    $script .= 'plainBuild(' . $modifier . ')';    
  }
  planetary_repo_save_file("/meta/inf/config/MathHub/build.tmp.mbt", $script);
  exec("/meta/inf/config/MathHub/run-tmp.sh");
}



function oaff_admin_get_build_lock_path() {
  $rel_lock = "meta/inf/config/MathHub/build.lock";
  $lock = planetary_repo_access_rel_path($rel_lock);

  if (planetary_repo_stat_file($rel_lock)) {
    return false;
  } else {
    return $lock;
  }
}




function oaff_admin_lmh_update() {
  $lmh_status = shell_exec('lmh update --all 2>&1');
  oaff_log("OAFF.ADMIN", "`lmh update --all` returned: <pre>$lmh_status</pre>");
  drupal_set_message('Success');
  return '';
}

function oaff_admin_libs_update() {
  $git_log = shell_exec('cd /var/data/localmh/ext/sTeX/ && git pull 2>&1 && cd /var/data/localmh/ext/MMT/ && svn up 2>&1');
  oaff_log("OAFF.ADMIN", "`git pull` returned: <pre>$git_log</pre>");
  drupal_set_message('Success');
  return '';
}

function oaff_admin_generate_glossary() {
  $mmt_config = variable_get("mmt_config");
  $mmturl = $mmt_config['mmturl'];
  $out = file_get_contents($mmturl . '/:planetary/generateGlossary');
  oaff_log("OAFF.ADMIN", "Regenerated Glossary");
  drupal_set_message($out);
  return '';
}