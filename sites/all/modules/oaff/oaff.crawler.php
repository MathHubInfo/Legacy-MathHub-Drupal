<?php

/*************************************************************************
* This file is part of the MathHub.info System  (http://mathhub.info).   *
* It is hosted at https://github.com/KWARC/MathHub                       *
* Copyright (c) 2015 by the KWARC group (http://kwarc.info)              *
* Licensed under GPL3, see http://www.gnu.org/licenses/gpl.html          *
**************************************************************************/
require_once 'oaff.base.php';

define("MAX_NODES_PER_SYNC", 1000);

function oaff_crawler_menu(& $items) {
  $items['mh/sync-nodes'] = array(
    'title' => "Sync Nodes",
    'page callback' => 'oaff_crawler_sync_nodes',
    'access callback' => 'oaff_admin_access',
    'menu_name' => MENU_CALLBACK
  );
  return $items;
}

function oaff_crawler_sync_nodes() {
  $oaff_config = variable_get("oaff_config");
  $oaff_config['crawler']['new_nodes'] = 0;
  $oaff_config['crawler']['deleted_nodes'] = 0;

  variable_set('oaff_config', $oaff_config);
  oaff_crawler_sync_help_docs(); //separate treatment for documentation in meta/inf (not standard mathhub content)
  oaff_crawler_sync_content();
  $oaff_config = variable_get("oaff_config");
  $nr_nodes = $oaff_config['crawler']['new_nodes'];
  $nr_del_nodes = $oaff_config['crawler']['deleted_nodes'];

  if ($nr_nodes == 0) {
    drupal_set_message("Successfully crawled archives, nothing new to import");
  } else {
    drupal_set_message("Successfully created $nr_nodes nodes");
  }
  if ($nr_del_nodes > 0) {
    drupal_set_message("Deleted $nr_del_nodes nodes");
  }
  $out = '<div> <button class="btn btn-primary " onclick="window.location = \'/mh/sync-nodes\'"> Continue </button> </div> ';
  return $out;
  return "";
}


/**
 * Crawls the filesystem for documentation nodes (typically html files in meta/inf/help)
 */
function oaff_crawler_sync_help_docs() {
  $docs_base = "/meta/inf/doc/help/";
  $files = planetary_repo_list($docs_base);
  $oaff_config = variable_get('oaff_config');
  foreach ($files as $filename) {
    $pathinfo = pathinfo($filename);
    $name = $pathinfo['filename'];
    $fullname = $pathinfo['basename'];
    $alias = oaff_base_join_path(array('help', $filename));
    $fpath = oaff_base_join_path(array($docs_base, $filename));
    $srcpath = drupal_lookup_path('source', $alias);
    if (!$srcpath) {
      if (isset($pathinfo['extension']) && $pathinfo['extension'] == "html") {
        $options = array();
        if ($fpath == "/meta/inf/doc/help/main.html") { //doc overview for frontpage
          $options = array("promote" => 1, "sticky" => 1);
        }
        if ($fpath == "/meta/inf/doc/help/documentation.html") { //main entry point of documentation
          $options = array('show_in_menu' => true, "plid" => $oaff_config['menus']['help']['mlid']);
        }
        oaff_base_make_help_doc($name, 'help/' . $fullname, $fpath, $options)['nid'];
      }
    }
  }
}

function oaff_crawler_sync_content() {
  $oaff_config = variable_get('oaff_config');
  $libs = $oaff_config['config']['libs'];
  $rid = $oaff_config['libraries_nid'];
  oaff_crawler_sync_top_metadata(); //info used to render libraries node
  $gids = array();
  foreach ($libs as $group => $archs) {
    $group_help_fpath = oaff_base_join_path(array($group, 'meta-inf', 'index.html'));
    oaff_crawler_sync_group_metadata($group); //info used to render group help node
    $gid = oaff_base_make_help_doc($group, $group, $group_help_fpath)['nid'];
    $gids[] = $gid;
    foreach ($archs as $archive => $format) {
      $archive_path = oaff_base_join_path(array($group, $archive));
      $archive_help_fpath = oaff_base_join_path(array($group, $archive, 'META-INF', 'index.html'));
      oaff_crawler_sync_archive_metadata($group, $archive); //info used to render group help node
      $aid = oaff_base_make_help_doc($archive, $archive_path, $archive_help_fpath)['nid'];
      $aids[] = $aid;
      $srcids = oaff_crawler_sync_archive($group, $archive, $oaff_config['config']['formats']);
      oaff_update_children($aid, $srcids);
      oaff_update_archive_statistics($group, $archive);
    }
    oaff_set_children($gid, $aids);
    oaff_update_group_statistics($group);
  }
  oaff_set_children($rid, $gids);
}

function oaff_crawler_sync_archive($group, $archive, $formats, $rel_path = "") {
  $parent_path = oaff_base_join_path(array($group, $archive, "source", $rel_path));
  $cids = array();
  $files = planetary_repo_list($parent_path);
  foreach ($files as $fname) {
    if ($fname[0] != '.' && preg_match('/all\..*/', $fname) != 1 && $fname != "localpaths.tex") { // ignoring hidden files and folders, 
                                                                              // also lmh/stex specific ones  
      $fpath = oaff_base_join_path(array($parent_path, $fname));
      if (planetary_repo_is_dir($fpath)) {
        $frel_path = $rel_path . "/" . $fname;
        $fcids = oaff_crawler_sync_archive($group, $archive, $formats, $frel_path);
        $fid = oaff_base_make_virt_doc($group, $archive, $frel_path)['nid']; 
        oaff_update_children($fid, $fcids);              
        $cids[] = $fid;
      } else {
        $curr_format = ''; //default
        foreach ($formats as $format => $compilers) { //checking if first compiler was run
          $log_path = oaff_base_join_path(array($group, $archive, "errors", $compilers[0], $rel_path, $fname . ".err"));          
          if (planetary_repo_stat_file($log_path)) {// this is the right format
            $curr_format = $format;
          }
        }
        if ($curr_format != '') { //if some format found
          $cids[] = oaff_base_make_oaff_doc($group, $archive, $rel_path, $fname, $curr_format)['nid'];
        }
      }
    }
    $oaff_config = variable_get('oaff_config');
    if ($oaff_config['crawler']['new_nodes'] >= MAX_NODES_PER_SYNC) {
      break;
    }
  }
  drupal_set_message("Synchronized archive $group/$archive ");
  return $cids;
}


/** Utilities for syncing meta-data */

function oaff_crawler_sync_top_metadata() {
  $oaff_config = variable_get('oaff_config');
 // getting info from manifest
  $mf_path = "meta/inf/MANIFEST.MF";
  $manifest = planetary_repo_load_file($mf_path);
  $mf_lines = explode("\n", $manifest);
  $props = array();//array of archive properties
  foreach ($mf_lines as $line) {
    $pair = explode(":", $line, 2);
    if (count($pair) == 2) { //ignoring invalid (e.g empty) lines 
      $props[$pair[0]] = $pair[1];
      if ($pair[0] == 'description') {
        $desc_file = trim($pair[1]);
        $props[$pair[0]] = planetary_repo_load_file(oaff_base_join_path(array("meta/inf/", $desc_file)));
      }
    }
  }
  $oaff_config['metadata']['top'] = $props;
  variable_set('oaff_config', $oaff_config);
  return $props;
}

function oaff_crawler_sync_group_metadata($group) {
  $oaff_config = variable_get('oaff_config');
  // getting info from manifest
  $mf_path = oaff_base_join_path(array($group, "meta-inf/MANIFEST.MF"));
  $manifest = planetary_repo_load_file($mf_path);
  $mf_lines = explode("\n", $manifest);
  $props = array();//array of archive properties
  foreach ($mf_lines as $line) {
    $pair = explode(":", $line, 2);
    if (count($pair) == 2) { //ignoring invalid (e.g empty) lines 
      $props[$pair[0]] = $pair[1];
      if ($pair[0] == 'description') {
        $desc_file = trim($pair[1]);
        $props[$pair[0]] = planetary_repo_load_file(oaff_base_join_path(array($group, "meta-inf", $desc_file)));
      }
    }
  }
  $oaff_config['metadata']['groups'][$group] = $props;
  variable_set('oaff_config', $oaff_config);
  return $props;
}

function oaff_crawler_sync_archive_metadata($group, $archive) {
  $oaff_config = variable_get('oaff_config');
  
  // getting info from manifest
  $mf_path = oaff_base_join_path(array($group, $archive, "META-INF/MANIFEST.MF"));
  
  $manifest = planetary_repo_load_file($mf_path);
  $mf_lines = explode("\n", $manifest);
  $docbase = 'http://docs.omdoc.org/default'; // default
  $id = $group . '/' . $archive; // default
  $props = array();//array of archive properties
  foreach ($mf_lines as $line) {
    $pair = explode(":", $line, 2);
    if (count($pair) == 2) { //ignoring invalid (e.g empty) lines 
      $props[$pair[0]] = trim($pair[1]);
      if (trim($pair[0]) == "narration-base") {
        $docbase = trim($pair[1]);
      } elseif (trim($pair[0]) == "id") {
        $id = trim($pair[1]);
      } elseif ($pair[0] == 'description') {
        $desc_file = trim($pair[1]);
        $props[$pair[0]] = planetary_repo_load_file(oaff_base_join_path(array($group, $archive, "META-INF", $desc_file)));
      }
    }
  }
  $all_props = array('docbase' => $docbase, 'id' => $id, 'props' => $props);
  $oaff_config['metadata']['archives'][$group][$archive] = $props;
  variable_set('oaff_config', $oaff_config);
  return $all_props;
}


function oaff_crawler_sync_config_file() {
  $inst_name = variable_get('site_name', 'mathhub');
  $config_file = "/meta/inf/config/" . $inst_name . "/config.mcf";
  $content = planetary_repo_load_file($config_file);
  //adding relevant data to in-mermory state 
  $oaff_config = variable_get('oaff_config');
  $oaff_config['config'] = array();
  $lines = explode("\n", $content);
  $section = ""; //default
  foreach ($lines as $line) {
    $line = trim($line);
    if ($line[0] == '#') {
      $section = substr($line,1);
    } else if ($line[0] == "/" && $line[1] == "/") {
      //comment line ignoring
    } else {
      $comps = explode(" ",$line);
      if ($section == "archives") {
        if (count($comps) == 2) {
          $segs = explode("/", $comps[0]);
          if (count($segs) == 2) {
            $oaff_config['config']['libs'][$segs[0]][$segs[1]] = $comps[1];
          } else {
            drupal_set_message("Ignoring invalid archives line (bad archive path): " . $line);
          }
        } else {
          drupal_set_message("Ignoring invalid archives line: " . $line);
        }
      } else if ($section == "formats") {
        if (count($comps) == 3) {
          $importers = explode(",", $comps[1]);
          $exporters = explode(",", $comps[2]);
          $oaff_config['config']['formats'][$comps[0]] = array_merge($importers, $exporters);
        } else {
          drupal_set_message("Ignoring invalid formats line: " . $line);
        }
      } else if ($section == "profiles") {
        if (count($comps) == 2) {
          $prof_archs = explode(",", $comps[1]);
          $oaff_config['config']['profiles'][$comps[0]] = $prof_archs;
        } else {
          drupal_set_message("Ignoring invalid profiles line: " . $line);
        }
      }
    }
  }
  variable_set('oaff_config', $oaff_config);
}