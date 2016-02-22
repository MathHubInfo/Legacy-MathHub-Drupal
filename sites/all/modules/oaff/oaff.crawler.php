<?php

/*************************************************************************
* This file is part of the MathHub.info System  (http://mathhub.info).   *
* It is hosted at https://github.com/KWARC/MathHub                       *
* Copyright (c) 2015 by the KWARC group (http://kwarc.info)              *
* Licensed under GPL3, see http://www.gnu.org/licenses/gpl.html          *
**************************************************************************/
require_once 'oaff.base.php';

define("MAX_NODES_PER_SYNC", 500);

function oaff_crawler_menu(& $items) {
  $items['mh/sync-nodes'] = array(
    'title' => "Sync Nodes",
    'page callback' => 'oaff_crawler_sync_nodes',
    'access callback' => true, //'oaff_admin_access',
    'menu_name' => MENU_CALLBACK
  );
  return $items;
}

function oaff_crawler_sync_nodes() {
  $oaff_config = variable_get("oaff_config");
  $oaff_config['crawler']['new_nodes'] = 0;
  $oaff_config['crawler']['deleted_nodes'] = 0;
  variable_set('oaff_config', $oaff_config);
  oaff_crawler_sync_text_formats();
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
}

/**
 * creates the text formats needed for archives based on the data in the MathHub config file
 * each MMT format gets a corresponding MathHub format with two filters: 
 *    a compilation filter that aggregates the importers in order
 *    a presentation filter that aggregates the exporters(e.g. presenters)
 * Formats are used for presentation (first exporter is default presenter), 
 * generating node aspects, error viewing and build management 
 */
function oaff_crawler_sync_text_formats() {
  $oaff_config = variable_get("oaff_config");
  $all_formats = $oaff_config['config']['formats'];
  $created_formats  = $oaff_config['crawler']['formats'];

  //creating formats (declared in the config file)
  foreach ($all_formats as $format => $comps) {
    if (!in_array($format, $created_formats)) { // new format
      $importers = $comps['importers'];
      $exporters = $comps['exporters'];
      $filters = array();
      if ($importers) {
        $filters['mmt-compilation'] = array(
          'status' => 1,
          'settings' => array(
            'mmt_format' => implode(" ", $importers),
          ),
          'weight' => -47,
        );
      }
      $filters['mmt-presentation'] = array(
        'status' => 1,
        'settings' => array(
          'mmt_style' => implode(" ", $exporters),
        ),
        'weight' => -46,
      );
      oaff_crawler_create_text_format($format, $filters, array(3));
      $oaff_config['crawler']['formats'][] = $format;
    }
  }
  variable_set('oaff_config', $oaff_config);
}


/**
* Create text format 
* @param $format_name The name of text format to create
* @param $filters Filters to activate for this format, array(filter name => params)
*   params -- associative array
*     status => integer -- 0 - inactive, 1 - active
*     weigth => integer -- (optional) 
*     settings => array -- params depending on filter
* @param $roles Array of rids of user roles who will have access to this format
*/
function oaff_crawler_create_text_format($format_name, $filters, $roles) {
  $format_format = str_replace(' ', '_', strtolower($format_name));
  $format = array(
    'format' => $format_format,
    'name' => $format_name,
    'filters' => $filters,
  );
  $format = (object) $format;
  // save format
  filter_format_save($format);
  
  // give permission to allowed users
  // use direct access to database due to 
  // absence of drupal built in function
  foreach ($roles as $key => $role) {
    db_merge('role_permission')
    ->key(array(
      'rid' => $role,
      'permission' => 'use text format ' . $format_format,
    ))
    ->fields(array(
      'module' => 'filter',
    ))
    ->execute();
  }

  drupal_set_message(t('A <a href="@php-code">' 
    . $format->name . '</a> text format has been created.', 
    array('@php-code' => url('admin/config/content/formats/' . $format->format))));
}

/**
* Delete text format
* @param $format_name Text format name to delete
*/
function oaff_crawler_delete_text_format($format_name) {
  $format_format = str_replace(' ', '_', strtolower($format_name));
  
  // use direct access to database due to 
  // absence of drupal built in functions

  // delete filters activated for this format
  db_delete('filter')
    ->condition('format', $format_format)
    ->execute();

  // delete format
  db_delete('filter_format')
    ->condition('format', $format_format)
    ->execute();

  //delete user permissions
  db_delete('role_permission')
    ->condition('permission', 'use text format ' . $format_format)
    ->execute();

  drupal_set_message(t('A ' . $format_name . ' text format has been deleted.'));
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
        if (in_array($fpath, $oaff_config['config']['mainpage_help'])) { //doc overview for frontpage
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
    $status = oaff_crawler_sync_group_metadata($group); //info used to render group help node
    if ($status) {
      $gid = oaff_base_make_help_doc($group, $group, $group_help_fpath)['nid'];
      $gids[] = $gid;
      $aids = array();
      foreach ($archs as $archive => $format) {
        $archive_path = oaff_base_join_path(array($group, $archive));
        $status = oaff_crawler_sync_archive_metadata($group, $archive); //info used to render group help node
        if ($status) {
          $archive_help_fpath = oaff_base_join_path(array($group, $archive, 'META-INF', 'index.html'));
          $aid = oaff_base_make_help_doc($archive, $archive_path, $archive_help_fpath, array("aspects" => array('svg')))['nid'];
          $aids[] = $aid;
          $srcids = oaff_crawler_sync_archive($group, $archive, $oaff_config['config']['formats']);
          oaff_update_children($aid, $srcids);
          //oaff_update_archive_statistics($group, $archive);
          drupal_set_message("Synchronized archive '$group/$archive' ");
        } else {
          drupal_set_message("Failed to sync archive '$group/$archive', missing manifest file (perhaps archive misconfigured or not installed).", "error");
        }
      }
      oaff_update_children($gid, $aids);
      //oaff_update_group_statistics($group);
    } else {
      drupal_set_message("Failed to sync group '$group', missing manifest file (perhaps its meta-inf archive misconfigured or not installed).", "error");
    }
  }
  oaff_update_children($rid, $gids);
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
          if ($compilers['importers']) { //non-empty importer array
            $log_path = oaff_base_join_path(array($group, $archive, "errors", $compilers['importers'][0], $rel_path, $fname . ".err"));          
            if (planetary_repo_stat_file($log_path)) {// this is the right format
              $curr_format = $format;
            }
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
  if (planetary_repo_stat_file($mf_path)) { //manifest file exists
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
    return true; //success
  } else {
    return false; //failed to sync group
  }
}

function oaff_crawler_sync_archive_metadata($group, $archive) {
  $oaff_config = variable_get('oaff_config');
  // getting info from manifest
  $mf_path = oaff_base_join_path(array($group, $archive, "META-INF/MANIFEST.MF"));
  if (planetary_repo_stat_file($mf_path)) { //manifest file exists
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
    return true; //success
  } else {
    return false; //failed to sync archive
  }
}


function oaff_crawler_sync_config_file() {
  $inst_name = variable_get('site_name', 'mathhub');
  $config_file = "/meta/inf/config/" . $inst_name . "/config.mcf";
  $content = planetary_repo_load_file($config_file);
  //adding relevant data to in-mermory state 
  $oaff_config = variable_get('oaff_config');
  $config = array('libs' => array(), 'formats' => array(), 'profiles' => array(), 
                  'external_libs' => array(), 'mainpage_help' => array());
  //creating the special folder format
  $config['formats']['folder'] = array("importers" => array(), "exporters" => array("planetary")); 

  $lines = explode("\n", $content);
  $section = ""; //default
  foreach ($lines as $line) {
    $line = trim($line);
    if ($line == "") {
      //nothing to do
    } else if ($line[0] == '#') {
      $section = substr($line,1);
      //section for single multi-line entry => initializing here
      if ($section == "external_lib") { 
        $config["external_libs"][] = array();
      }
    } else if ($line[0] == "/" && $line[1] == "/") {
      //comment line ignoring
    } else {
      $comps = explode(" ",$line);
      if ($section == "archives") {
        if (count($comps) == 2) {
          $segs = explode("/", $comps[0]);
          if (count($segs) == 2) {
            $config['libs'][$segs[0]][$segs[1]] = $comps[1];
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
          $config['formats'][$comps[0]] = array(
            'importers' => $importers,
            'exporters' => $exporters,
          );
        } else {
          drupal_set_message("Ignoring invalid formats line: " . $line);
        }
      } else if ($section == "profiles") {
        if (count($comps) == 2) {
          $prof_archs = explode(",", $comps[1]);
          $config['profiles'][$comps[0]] = $prof_archs;
        } else {
          drupal_set_message("Ignoring invalid profiles line: " . $line);
        }
      } else if ($section == "external_lib") {
        $value = implode(' ',array_slice($comps, 1));
        $n = count($config['external_libs']) - 1;
        $config['external_libs'][$n][$comps[0]] = $value;
      } else if ($section == "mainpage_help") {
        $config['mainpage_help'][] = trim($line);
      } else if ($section == "glossary") {
        $config['glossary'] = trim($line);
      }
    }
  }
  $oaff_config['config'] = $config;
  variable_set('oaff_config', $oaff_config);
}