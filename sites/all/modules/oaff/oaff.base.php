<?php

/*************************************************************************
* This file is part of the MathHub.info System  (http://mathhub.info).   *
* It is hosted at https://github.com/KWARC/MathHub                       *
* Copyright (c) 2015 by the KWARC group (http://kwarc.info)              *
* Licensed under GPL3, see http://www.gnu.org/licenses/gpl.html          *
**************************************************************************/

/**
 * Function to setup OAFF-specific functionality (CSS & JS) on page
 * OAFF-feature modules (e.g. MMT) define their own initialize methods 
 * to load further features (e.g. JOBAD modules)
 * Typically called from hook_node_view (i.e. for OAFF, oaff_node_view)
 * @param $location Path
 * @param $text_format Text format
 */
function oaff_base_initialize($location = NULL, $text_format = "", $aspect = "pres") {
    if ($location != NULL) {
      // oaff_initialize
      $pathinfo = oaff_base_get_path_info($location);
      $archive = $pathinfo['archive'];
      $group = $pathinfo['group'];
      $rel_path = $pathinfo['rel_path'];
      
      //adding data as js variables (used by JS/JOBAD modules)
      drupal_add_js("var oaff_node_group = '$group';", "inline");
      drupal_add_js("var oaff_node_archive = '$archive';", "inline");
      drupal_add_js("var oaff_node_rel_path = '$rel_path';", "inline");      

      /* CSS */
      $mmt_path = drupal_get_path('module', 'mmt');
      $mmt_config = variable_get('mmt_config');
      $mmt_url = $mmt_config['mmturl'];  
      $oaff_config = variable_get('oaff_config');
      $compilers = array(); //default
      if (isset($oaff_config['config']['formats'][$text_format])) {
        $compilers = $oaff_config['config']['formats'][$text_format]['importers'];
        $exporters = $oaff_config['config']['formats'][$text_format]['exporters'];
        if (in_array($aspect, $exporters)) {
          $compilers[] = $aspect;
        } else {
          $compilers[] = $exporters[0]; // defaulting to first exporter as default aspect
        }
      }

      // common JS+CSS
      drupal_add_css($mmt_path . '/css/mmt.css', array('weight' => PHP_INT_MAX, 'every_page' => false));
      drupal_add_js($mmt_path . '/utils/mmt-html.js', 'file', array('cache' => false));
      drupal_add_js($mmt_path . '/utils/mmt-planetary.js', 'file', array('cache' => false));
      drupal_add_js($mmt_path . '/utils/planetary-localization.js', 'file', array('cache' => false));
      drupal_add_js($mmt_path . '/utils/mathml.js', 'file', array('cache' => false));
      jobad_add_module($mmt_path . '/jobad/planetary-navigation.js', "kwarc.mmt.planetary.navigation");
      jobad_add_module($mmt_path . '/jobad/ontology-navigation.js', "kwarc.mmt.ontology.navigation");
      jobad_add_module($mmt_path . '/jobad/hovering.js', "kwarc.mmt.hovering");

      foreach ($compilers as $compiler) {
        switch ($compiler) {
          case 'mh-html': 
              drupal_add_css($mmt_path . '/css/browser.css', array('weight' => PHP_INT_MAX, 'every_page' => false));
              jobad_add_module($mmt_path . '/jobad/interactive-viewing.js', "kwarc.mmt.intvw");
              jobad_add_module($mmt_path . '/jobad/planetary-gitlab.js', "kwarc.mmt.planetary.gitlab"); 
            break;
         case 'planetary':
            jobad_add_module($mmt_path . '/jobad/planetary-gitlab.js', "kwarc.mmt.planetary.gitlab");
            break;
          case 'svg':
              drupal_add_css($mmt_path . '/css/browser.css', array('weight' => PHP_INT_MAX, 'every_page' => false));
            break;
          case 'latexml':
            drupal_add_css($mmt_path . '/css/latexml.css', array('weight' => PHP_INT_MAX, 'every_page' => false));
            break;
          default:
            break;
        }
      }
      /* JavaScript */
      drupal_add_js('var mmtUrl = "' . $mmt_url . '";', 'inline');
      // if search enabled
      // jobad_add_module($mmt_path . '/jobad/search.js', "kwarc.mmt.search");
    }  

    libraries_load("jobad");
    // modules are loaded in specific oaff extensions (e.g. MMT)
    jobad_add_module("/sites/all/libraries/jobad/modules/core/mathjax.mathjax.js", "mathjax.mathjax");
    
    $inst_name = jobad_initialize();
    return $inst_name; 
}


/** Node Creation Utilities */
function oaff_base_make_oaff_doc($group, $archive, $rel_path, $item, $format) {
  $fpath = oaff_base_join_path(array($group, $archive, "source", $rel_path, $item));
  $pathinfo = oaff_base_get_path_info($fpath);
  $title = $pathinfo['title'];
  $alias = $pathinfo['alias'];
  $content = array('field_external' => array('path' => $fpath, 'filter' => $format));
  $options = array('lang' => $pathinfo['lang'], 'aspects' => array('omdoc', 'svg', 'source', 'edit'));
  $res = oaff_base_sync_node($title, $alias, "oaff_doc", $content, $options);
  return $res;
}

function oaff_base_make_virt_doc($group, $archive, $rel_path) {
  $fpath = oaff_base_join_path(array($group, $archive, "source", $rel_path));
  $pathinfo = oaff_base_get_path_info($fpath);
  $title = $archive; // default
  if ($rel_path != "") {
    $title = $pathinfo['title'];
  }
  $alias = $pathinfo['alias'];
  $content = array('field_external' => array('path' => $fpath, 'filter' => 'folder'));
  $res = oaff_base_sync_node($title, $alias, "oaff_virtdoc", $content);
  return $res;
}

function oaff_base_make_help_doc($title, $alias, $fpath, $options = array()) {
  $content = array('field_external' => array('path' => $fpath));
  $res = oaff_base_sync_node($title, $alias, "oaff_helpdoc", $content, $options);
  return $res;
}

function oaff_base_make_static_doc($title, $alias, $body, $plid = NULL, $expanded = 0, $weight = 10) {
  $content = array('body' => array('value' => $body, 'format' => 'full_html'));
  $options = array('show_in_menu' => true, 'menu_plid' => $plid, 'menu_expanded' => $expanded, 'menu_weight' => $weight);
  $res = oaff_base_sync_node($title, $alias, "page", $content, $options);
  return $res;
}

/**
 * Create a new node only if the alias (URL) is new (i.e. it doesn't already exist)
 * returns id of the node at that alias (new or old)
 */
function oaff_base_sync_node($title, $alias, $node_type, $content, $options = array()) {
  $node_path = drupal_lookup_path('source', $alias);
  if (!$node_path) {
    // creating node
    $res = oaff_base_create_node($title, $alias, $node_type, $content, $options);
    $oaff_config = variable_get('oaff_config');
    $oaff_config['crawler']['new_nodes'] += 1;
    if (isset($res['mlid'])) {
      $oaff_config['crawler']['mlids'][] = $res['mlid'];
    }
    if (!in_array($node_type, $oaff_config['all_node_types'])) {
      $oaff_config['crawler']['nids'][] = $res['nid']; 
    }
    variable_set('oaff_config', $oaff_config);
  } else {
    $res = array('nid' => explode("/", $node_path)[1]);
  }
  return $res;
}

/**
 * create an general oaff node, called by 
 * @param $title the node title (module name in smglom)
 * @param $alias, the mathhub url for the node
 * @param $node_type the type of the node
 * @param $content the values for the node fields (e.g. `body` or `field_external`)
 * @param $options an array of additional options for the node, see the $defaults array below for more info
 * @return array with `nid` the node id and optionally `mlid` the id of the menu link created (if any)
 */
function oaff_base_create_node($title, $alias, $node_type, $content, $options = array()) {
  // settings basic note options
  $defaults = array('promote' => 0, 'sticky' => 0, 'lang' => 'und', 'show_in_menu' => false, 
    'menu_plid' => NULL,  'menu_expanded' => 0, 'menu_weight' => 10, 'aspects' => array());
  $opt_vals = array_merge($defaults, $options);
  $newnode = (object) array(
   'type' => $node_type,
   'uid' => 0,
   'created' => strtotime("now"),
   'changed' => strtotime("now"),
   'status' => 1, //published
   'comment' => 0, //comments disabled 
   'promote' => $opt_vals['promote'],
   'moderate' => 0,
   'sticky' => $opt_vals['sticky'],
   'language' => $opt_vals['lang'],
  );
  //setting node fields
  $newnode->title = $title;
  if (isset($content['body'])) {
    $newnode->body['und']['0'] = $content['body'];
  }
  if (isset($content['field_external'])) {
    $newnode->field_external['und']['0'] = $content['field_external'];
  }
  node_object_prepare($newnode);// necessary ?
  node_save($newnode);
  //setting node alias(es)
  oaff_base_save_alias($newnode->nid, $alias);
  foreach ($opt_vals['aspects'] as $aspect) {
    oaff_base_save_alias($newnode->nid, $alias, $aspect);
  }
  //adding menu entry (if set)
  $src_url = 'node/' . $newnode->nid;
  if ($opt_vals['show_in_menu']) { 
    $mlid = oaff_create_menu_link($src_url, ucfirst($title), $opt_vals['menu_weight'], $opt_vals['menu_plid'], $opt_vals['menu_expanded']);
    return array('nid' => $newnode->nid, 'mlid' => $mlid);
  } else {
    return array('nid' => $newnode->nid);
  }
}

/**
 * saves a path alias for a node, used by oaff_node_insert
 * @param $nid the node id
 * @param $path the alias to be saved (the MMT URI of the node)
 */ 
function oaff_base_save_alias($nid, $path, $aspect = '') {
  // saving path
  if (substr($path, -1) == '/') {
    $path = substr($path, 0, -1);
  }
  $source = 'node/' . $nid;
  if ($aspect != '') {
    $source = $source . '/' . $aspect;
    $path = $path . '!' . $aspect; 
  }
  $path_opt = array(
    'source' => $source,
    'alias' => $path,
  );
  path_save($path_opt);
}



/**
 * builds information about a physical file location, including the archives it belongs in, filename, extension, etc
 * @param $location the file path 
 * @return an array with the path information
 */
function oaff_base_get_path_info($location) {
  $oaff_path_info = array();
  $oaff_path_info['location'] = $location;

  // location format is <group-name>/<archive-name>/source/<fragment path>
  $pathinfo = pathinfo($location);
  //title (filename without extension)
  $title = $pathinfo['filename'];
  $oaff_path_info['title'] = $title;
  // name of file
  $filename = $pathinfo['basename']; 
  $oaff_path_info['filename'] = $filename;
  // extension
  if (isset($pathinfo['extension'])) {
    $oaff_path_info['extension'] = $pathinfo['extension'];
  }
  
  //parent folder
  $parent = dirname($location);
  $oaff_path_info['parent'] = $parent; 

  //mathhub path fragments
  $loc_segs = explode('/', $location);
  $oaff_path_info['group'] = $loc_segs[0];
  $oaff_path_info['archive'] = $loc_segs[1];
  $oaff_path_info['dimension'] = $loc_segs[2];
  $oaff_path_info['rel_path'] = oaff_base_join_path(array_slice($loc_segs, 3));
  $oaff_path_info['rel_parent'] = oaff_base_join_path(array_slice($loc_segs, 3, -1));

  //language (only for selected libs)
  $lang = 'und'; // default;
  if ($oaff_path_info['group'] == 'smglom') { //hacking smglom specific language detection (mod.<lang>.tex) 
    $title_info = pathinfo($title);
    $oaff_path_info['smglom_mod'] = $title_info['filename'];
    if (isset($title_info['extension'])) {
      $lang = $title_info['extension'];
    }
  }
  $oaff_path_info['lang'] = $lang;

  
  //alias (removing 'source' from file location and changing extension to .omdoc) 
  $aliasName = $title;
  if ($title != $filename) { //otherwise no extension => nothing to change (probably folder)
    $aliasName = $title . ".omdoc";
  }

  $alias = oaff_base_join_path(array($oaff_path_info['group'], $oaff_path_info['archive'], $oaff_path_info['rel_parent'], $aliasName)); 
  $oaff_path_info['alias'] = $alias;
  
  return $oaff_path_info;
}

function oaff_base_join_path($arguments) {
  $path = '';
  $args = array();
  foreach ($arguments as $a) {
    if ($a !== '') {
      $args[] = $a;
    }
  }
  $arg_count = count($args);
  for ($i = 0; $i < $arg_count; $i++) {
    $folder = $args[$i];
    if ($i != 0 and $folder[0] == DIRECTORY_SEPARATOR) {
      $folder = substr($folder, 1);
    } 
    if ($i != $arg_count - 1 and substr($folder, -1) == DIRECTORY_SEPARATOR) {
      $folder = substr($folder, 0, -1);
    }
    $path .= $folder;
    if ($i != $arg_count - 1) {
      $path .= DIRECTORY_SEPARATOR;
    }
  }
  return $path;
}