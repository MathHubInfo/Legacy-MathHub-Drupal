<?php

function oaff_features_menu(& $items) {
  $items['mh/broken-docs'] = array(
    'title' => "Broken Documents",
    'page callback' => 'oaff_features_broken_nodes',
    'access callback' => true,
    'type' => MENU_CALLBACK,
  );
  $items['mh/common-errors'] = array(
    'title' => "Common Errors",
    'page callback' => 'oaff_features_common_errors',
    'access callback' => true,
    'type' => MENU_CALLBACK,
  );

  $items['mh/add-document'] = array(
    'title' => "Add Document",
    'page callback' => 'oaff_features_add_doc',
    'access callback' => true,
    'type' => MENU_CALLBACK,
  );
  $items['mh/latest-updates'] = array(
    'title' => "Latest Updates",
    'page callback' => 'oaff_features_todo',
    'access callback' => true,
    'type' => MENU_CALLBACK,
  );
  $items['mh/show-questions'] = array(
    'title' => "User Questions",
    'page callback' => 'oaff_features_todo',
    'access callback' => true,
    'type' => MENU_CALLBACK,
  );
  return $items;
}

function oaff_features_add_doc() {
  return drupal_get_form('oaff_features_add_doc_form');
}

function oaff_features_todo() {
  return "<p> Coming soon...</p>";
}

function oaff_features_add_doc_form() {
  $form = array();

  $form['archive'] = array(
    '#type' => 'textfield',
    '#title' => 'Archive',
    '#required' => true,
  );
  $form['title'] = array(
    '#type' => 'textfield',
    '#title' => 'Module Name',
    '#required' => true,
  );
  $form['body'] = array(
    '#type' => 'text_format',
    '#title' => 'Document Body',
    '#required' => true,
  );

  $form['submit'] = array(
    '#type' => 'submit', 
    '#value' => t('Create Document')
  );
  $form['#submit'] = array('oaff_features_add_doc_callback');
  return $form;
}

function oaff_features_add_doc_callback($form, &$form_state) {
  $archive = $form_state['values']['archive'];
  $title = $form_state['values']['title'];
  $body = $form_state['values']['body'];

  $oaff_config = variable_get('oaff_config');
  $format = $oaff_config['formats'][$archive];
  $extension = $oaff_config['extensions'][$format];
  $location = $archive . '/source/' . $title . "." . $extension;
  planetary_repo_save_file($location, $body);
  drupal_set_message("Created document " . $title);
  $nid = oaff_create_oaff_doc($location, $archive);
  $form_state['redirect'] = 'node/'. $nid;
}

function oaff_features_common_errors() {
  $results = db_select('oaff_errors', 'e')
             ->fields('e', array('nid', 'type', 'compiler', 'short_msg'))
             ->execute()
             ->fetchAll();

  if (count($results) == 0) {
    drupal_set_message("No errors found");
    return "";
  }
  $compilers = array();
  foreach ($results as $result) {
    $msg = $result->short_msg;
    $type = $result->type;
    $compiler = $result->compiler;
    if (!isset($compilers[$compiler])) {
      $compilers[$compiler] = array();
    }
    if (!isset($compilers[$compiler][$type])) {
      $compilers[$compiler][$type] = array();
    }
    if (!isset($compilers[$compiler][$type][$msg])) {
      $compilers[$compiler][$type][$msg] = array('occurs' => 0, 'nids' => array());
    }
    $compilers[$compiler][$type][$msg]['occurs'] += 1; 
    $compilers[$compiler][$type][$msg]['nids'][] = $result->nid; 
     
  }

  $comp = function($a, $b) {
    return $a['occurs'] < $b['occurs'];
  };

  $errors = array();
  foreach ($compilers as $compiler => $types) {
    foreach ($types as $type => $msgs) {
      foreach ($msgs as $msg => $occurs) {
        $errors[] = array(
          'compiler' => $compiler,
          'type' => $type,
          'msg' => check_plain($msg),
          'occurs' => $occurs['occurs'],
          'nids' => $occurs['nids'],
        );
      }
    }
  }
  usort($errors, $comp); 

  $name_map = array(0 => 'Info', 1 => "Warning", 2 => "Error", 3 =>  "Fatal Error");
  $color_map = array(0 => '#9999FF', 1 => "#BBBB11", 2 => "#FF6666", 3 =>  "#FF2222");
  $class_map = array(0 => 'text-info', 1 => "text-warning", 2 => "text-danger", 3 =>  "text-danger");
  $out = '<div>';
  $i = 0;
  foreach ($errors as $error) {
    $out .= '<div class="node-teaser">';
    $out .= '<h4><span class="' . $class_map[$error['type']] . '"> ' . $error['msg'] . '  </span></h4>';
    $out .= '<div class="links"><ul class="list-inline">';
    $out .= '<li><span > ' . $error['compiler'] . ' compiler,</span></li>';
    $out .= '<li><span > ' . $error['occurs'] . ' occurrences, </span></li>';
    $out .= '<li><a style="cursor:pointer;" onclick="if (jQuery(this).html() == \'Show All\') {jQuery(this).html(\'Hide All\')} else {jQuery(this).html(\'Show All\')}; jQuery(\'#oaff_error_log' . $i . '\' ).toggle( \'fold\' );" > See All </a> </li>';
    $out .= '<div id="oaff_error_log' . $i . '" style="display: none;"><ul>';
    $nids = array_count_values($error['nids']);
    foreach ($nids as $nid => $count) {
      $alias = drupal_lookup_path('alias', 'node/' . $nid);
      $title = db_select('node', 'n')
          ->fields('n', array('title'))
          ->condition('n.nid', $nid)
          ->execute()
          ->fetchAssoc()['title'];
      $occurs = $count. ' occurences';
      if ($count == 1) {
        $occurs = $count. ' occurence';
      }
      $out .= '<li><a href="/' . $alias . '"> ' . $title . ' </a> <span>'. $occurs . '</span> </li>';
    }
    $out .= '</ul></div>';
    $out .= '</ul></div>';
    $out .= '</div>';
    $out .= '<hr/>';
    $node = node_view(node_load($error['nids'][0]), 'teaser');
    // $out .= drupal_render($node);
    $i += 1;
  }
  $out .= '</div>';

  return $out;
}


function oaff_features_broken_nodes() {
  $results = db_select('oaff_errors', 'e')
          ->fields('e', array('nid', 'type'))
          ->execute()
          ->fetchAll();
  if (count($results) == 0) {
    drupal_set_message("No documents have errors");
    return "";
  }

  $nodes = array();
  foreach ($results as $result) {
    if (!isset($nodes[$result->nid])) {
      $nodes[$result->nid] = array(0 => 0, 1 => 0, 2 => 0, 3 => 0);
    }
    $nodes[$result->nid][$result->type] += 1;
  }

  $query = db_select('node', 'n');
  $query->join('users', 'u', 'u.uid = n.uid');
  $results = $query    
    ->fields('n', array('title', 'nid', 'created'))
    ->fields('u', array('name', 'uid'))
    ->condition('n.nid', array_keys($nodes), "IN")
    ->execute()
    ->fetchAll();
  $msg = "<div>";
  /*
  usort($results, function($a, $b) {
      return ($a->status < $b->status);
    });
  */
  foreach ($results as $entry) {
    $alias = drupal_lookup_path('alias', 'node/' . $entry->nid);
    $msg .= '<div class="views-row">';
    $msg .= '<div class="node-teaser contextual-links-region" id="node-' . $entry->nid . '">';
    $msg .= '<h3> <a href="/' . $alias . '">' . $entry->title. '</a></h3>';
    $msg .= '<span class="submitted"><span >' . date("D M j, Y, g:i a", $entry->created) . ' &mdash; <a class="username" title="View user profile." href="/user/' . $entry->uid . ' ">' . $entry->name . '</a></span></span>';
    $msg .= '<div class="clearfix">
          <div class="links"><ul class="links list-inline"><li><span>';
    $infos = $nodes[$entry->nid][0];
    $warnings = $nodes[$entry->nid][1];
    $errors = $nodes[$entry->nid][2];
    $fatals = $nodes[$entry->nid][3];
    if ($infos == 1) {
      $msg .= '<span style="color:#9999FF">' . $infos . " info</span>, "; 
    } elseif ($infos != 0) {
      $msg .= '<span style="color:#9999FF">' . $infos . " infos</span>, "; 
    } 
    if ($warnings == 1) {
      $msg .= '<span style="color:#BBBB11">' . $warnings . " warning</span>, ";
    } elseif ($warnings != 0) {
      $msg .= '<span style="color:#BBBB11">' . $warnings . " warnings</span>, ";
    } 
    if ($errors == 1) {
      $msg .= '<span style="color:#FF6666">' . $errors . " error</span>, ";
    } elseif ($errors != 0) {
      $msg .= '<span style="color:#FF6666">' . $errors . " errors</span>, ";
    } 
    if ($fatals == 1) {
      $msg .= '<span style="color:#FF2222">' . $fatals . " fatal</span>, ";
    } elseif ($fatals != 0) {
      $msg .= '<span style="color:#FF2222">' . $fatals . " fatals</span>, ";
    }
    $msg = substr($msg, 0, -1); // removing last comma
    $msg .= "</span></li>";
    $msg .= '<li ><a title="' . $entry->title .'" href="/' . $alias . '">See details</a></li></ul></div></div>';
    $msg .= "</div></div>";
    $msg .= "<hr/>";
  }
  $msg .= "</div>";
  return $msg;
}