<?php

/*************************************************************************
* This file is part of the MathHub.info System  (http://mathhub.info).   *
* It is hosted at https://github.com/KWARC/MathHub                       *
* Copyright (c) 2015 by the KWARC group (http://kwarc.info)              *
* Licensed under GPL3, see http://www.gnu.org/licenses/gpl.html          *
**************************************************************************/

function oaff_features_menu(& $items) {
  $oaff_config = variable_get('oaff_config');
  $items['mh/broken-docs'] = array(
    'title' => "Broken Documents",
    'page callback' => 'oaff_features_broken_nodes',
    'access callback' => true,
    'type' => MENU_CALLBACK,
  );
  $items['mh/common-errors'] = array(
    'title' => "Build Errors",
    'page callback' => 'oaff_features_common_errors',
    'access callback' => true,
    'type' => MENU_NORMAL_ITEM,
    'weight' => 15,
    'plid' => $oaff_config['menus']['libs']['mlid'],
  );
  $items['mh/recrawl-error'] = array(
    'title' => "Recrawl Error",
    'page callback' => 'oaff_features_recrawl_errors',
    'access callback' => 'oaff_admin_access',
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

  $items['mh/dictionary'] = array(
    'title' => "Math Dictionary",
    'page callback' => 'oaff_multi_dictionary',
    'access callback' => true,
    'type' => MENU_NORMAL_ITEM,
    'plid' => $oaff_config['menus']['serv']['mlid'],
  );

  return $items;
}


function oaff_multi_dictionary() {
   drupal_add_css('
    .tt-input, .tt-hint {
    width: 396px;
    height: 30px;
    padding: 8px 12px;
    font-size: 24px;
    line-height: 30px;
    border: 2px solid #ccc;
    border-radius: 8px;
    outline: none;
}

.tt-input {
    box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
}

.tt-hint {
    color: #999
}

.tt-menu {
    width: 422px;
    max-height: 200 px;
    margin-top: 12px;
    padding: 8px 0;
    background-color: #fff;
    border: 1px solid #ccc;
    border: 1px solid rgba(0, 0, 0, 0.2);
    border-radius: 8px;
    box-shadow: 0 5px 10px rgba(0,0,0,.2);
}

.tt-suggestion {
    padding: 3px 20px;
    font-size: 18px;
    line-height: 24px;
}

.tt-suggestion.tt-cursor { /* UPDATE: newer versions use .tt-suggestion.tt-cursor */
    color: #fff;
    background-color: #0097cf;

}

.tt-suggestion p {
    margin: 0;
}

    ', "inline");
  
  $html = '<p>The math dictionary on this page is a service based on the <a href="https://mathhub.info/smglom">SMGloM</a> terminology. 
          To translate mathematical terms, select the source and target languages and enter them in the text window on the left (autocompletion). 
          The translations are hyperlinked to their respective definitions for convenience.</p>';
  $html .= '<div class="form-group">
    <div class="row">
        <div class="col-md-1">From:</div>
        <div class="col-md-2">
          <select id="tr_from_lang" onchange="th_auto()" class="form-control">
            <option>de</option>
            <option selected>en</option>
            <option>ro</option>
            <option>tr</option>
          </select>
        </div>
        <div class="col-md-1">To:</div>
        <div class="col-md-2">
          <select id="tr_to_lang" onchange="th_auto()" class="form-control">
            <option>de</option>
            <option>en</option>
            <option>ro</option>
            <option>tr</option>
          </select>
        </div>
      <div class="col-md-2">
        <button id="btn_translate" onclick="on_translate()" type="submit" class="btn btn-primary">Translate</button>
      </div>
    </div>
    <br/>
    <div class="row">
      <div class="col-md-6">
        <input id="tr_term" type="text" class="form-control" placeholder="Math Term">
      </div>
      <div class="col-md-6" >
        <div id="tr_out_term" type="text" class="form-control" placeholder="Translation" disabled> </div>
      </div>
    </div>
    
  </div>';
  drupal_add_js('misc/typeahead.bundle.min.js', 'file');
  $json = planetary_repo_load_file("dict_data.json");
  $links_json = planetary_repo_load_file("term_links.json");
  drupal_add_js('
    var dict_json_txt = \'' . $json .'\';
    var dict_json = JSON.parse(dict_json_txt);
    var links_json_txt = \'' . $links_json .'\';
    var links_json = JSON.parse(links_json_txt);
    
    var get_dict_keys = function() {
      var tr_from_lang = jQuery("#tr_from_lang").val();
      var tr_to_lang = jQuery("#tr_to_lang").val();
      var map = dict_json[tr_from_lang][tr_to_lang];

      var keys = [], name;
      for (name in map) {
        if (map.hasOwnProperty(name)) {
          keys.push(name);
        }
      }
      return keys;
    }
    ','inline');
  drupal_add_js('
  jQuery(document).ready(function () {
    jQuery("#tr_term").keypress(function(e) {
      if (e.which==13) {
        on_translate();
      }
    })
  });
  	','inline');

  drupal_add_js('
    var substringMatcher = function() {
      var strs = get_dict_keys();
      return function findMatches(q, cb) {
        var matches, substrRegex;
        matches = [];
        substrRegex = new RegExp(q, \'i\');
        jQuery.each(strs, function(i, str) {
          if (substrRegex.test(str)) {
            matches.push({ value: str });
          } 
        }); 
        cb(matches);
      };
    };

    var th_auto = function() {
      jQuery(\'#tr_term\').typeahead(\'destroy\');
      jQuery(\'#tr_term\').typeahead({
        hint: true,
        highlight: true,
        minLength: 1
      },
      {
        name: \'words\',
        displayKey: \'value\',
        source: substringMatcher()
      });
    }; 
    jQuery(function() {
      th_auto();
    });
    ', 'inline');

  drupal_add_js('
    var on_translate = function() {
      var tr_term = jQuery("#tr_term").val();
      var tr_from_lang = jQuery("#tr_from_lang").val();
      var tr_to_lang = jQuery("#tr_to_lang").val();

      var res = dict_json[tr_from_lang][tr_to_lang][tr_term];
      var html = "";
      for (var i = 0; i < res.length; i++) {
        var name = res[i];
        var link = links_json[name];
        html = html + "<a href=\"" + link + "\">" + name + "<a/>";
        if (i+1==res.length) {
        	html = html + " ";
        } else {
        	html = html + ", ";
        }
      }
      jQuery("#tr_out_term").html(html);
      };
    ', 'inline');  
  return $html;
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

//show common errors
function oaff_features_common_errors() {
  // query for database
  $query = db_select('oaff_errors', 'e')
             ->fields('e', array('eid', 'nid', 'type', 'compiler', 'mh_group', 'mh_archive', 'short_msg'));
  
  // initialize array for filtering different types of errors
  $err_checked = array("0" => false,"1" => false,"2" => false,"3" => false);
  // link to generate links to different cairo_pattern_get_surface(pattern)
  $pagelink = 'common-errors?';
  
  // get array with types of error to filtering
  if (isset($_GET['types'])) {
    $types = explode(",",$_GET['types']);
    $pagelink .= 'types='.$_GET['types'].'&';
  } else { //assuming all errors and fatal errors 
    $types = array("2", "3"); //default
  }

  // initialize array of types of errors with values from the client
  foreach ($types as $type) {
    $err_checked[$type] = true;
  }

  $query->condition('e.type', $types, "IN");

  function err_lvl_str($type, $err_map, $str_true, $str_false = "") {
    if ($err_map[$type]) {
      return $str_true;
    } else {
      return $str_false;
    }
  }

  $err_fields = array("compilers" => "", "groups" => "", "archives" => "");
  // get values for the fields from the client, fetch them to the query
  // and initialize values of these fields to received values
  if (isset($_GET['compilers'])) {
    $err_fields['compilers'] = 'value="' . $_GET['compilers'] . '"';
    $compilers = explode(",",$_GET['compilers']);
    $query->condition('e.compiler', $compilers, "IN");
    $pagelink .= 'compilers='.$_GET['compilers'].'&';

  } 
  if (isset($_GET['groups'])) {
    $err_fields['groups'] = 'value="' . $_GET['groups'] . '"';
    $groups = explode(",",$_GET['groups']);
    $query->condition('e.mh_group', $groups, "IN");
    $pagelink .= 'groups='.$_GET['groups'].'&';
  }
  if (isset($_GET['archives'])) {
    $err_fields['archives'] = 'value="' . $_GET['archives'] . '"';
    $archives = explode(",",$_GET['archives']);
    $query->condition('e.mh_archive', $archives, "IN");
    $pagelink .= 'archives='.$_GET['archives'].'&';
  }

  // execute query
  $results = $query->execute()
             ->fetchAll();

  $compilers = array();
  $statistics = array();
  foreach ($results as $result) {
    $msg = $result->short_msg;
    $type = $result->type;
    $compiler = $result->compiler;
    $group = $result->mh_group;
    $archive = $result->mh_archive;
    $eid = $result->eid;
    //initializing
    if (!isset($statistics[$group])) {
      $statistics[$group] = array("occurs" => array("0" => 0, "1" => 0, "2" => 0, "3" => 0), 'archives' => array());
    }
    if (!isset($statistics[$group]['archives'][$archive])) {
      $statistics[$group]['archives'][$archive] = array("0" => 0, "1" => 0, "2" => 0, "3" => 0);
    }
    if (!isset($compilers[$compiler])) {
      $compilers[$compiler] = array();
      foreach (array("0","1","2","3") as $tmptype) {
        $compilers[$compiler][$tmptype] = array('msgs' => array(), 'occurs' => 0);
      }
    }
    if (!isset($compilers[$compiler][$type]['msgs'][$msg])) {
      $compilers[$compiler][$type]['msgs'][$msg] = array('occurs' => 0, 'nids' => array(), 'eid' => $eid);
    }
    $statistics[$group]['occurs'][$type] += 1;
    $statistics[$group]['archives'][$archive][$type] += 1;
    
    $compilers[$compiler][$type]['occurs'] += 1;   
    $compilers[$compiler][$type]['msgs'][$msg]['occurs'] += 1; 
    $compilers[$compiler][$type]['msgs'][$msg]['nids'][] = $result->nid; 
     
  }

  $comp = function($a, $b) {
    if ($a['type'] != $b['type']) {
      return $a['type'] < $b['type'];
    } else if ($a['occurs'] != $b['occurs']) {
      return $a['occurs'] < $b['occurs'];
    } else {
      return $a['msg'] < $b['msg'];
    }
  };

  $errors = array();
  foreach ($compilers as $compiler => $types) {
    foreach ($types as $type => $msgs) {
      foreach ($msgs['msgs'] as $msg => $occurs) {
        $errors[] = array(
          'compiler' => $compiler,
          'type' => $type,
          'msg' => check_plain($msg),
          'occurs' => $occurs['occurs'],
          'nids' => $occurs['nids'],
          'eid'=> $occurs['eid'],
        );
      }
    }
  }
  usort($errors, $comp); 

  $name_map = array(0 => 'Info', 1 => "Warning", 2 => "Error", 3 =>  "Fatal Error");
  $color_map = array(0 => '#9999FF', 1 => "#BBBB11", 2 => "#FF6666", 3 =>  "#FF2222");
  //$class_map = array(0 => 'text-info', 1 => "text-warning", 2 => "text-danger", 3 =>  "text-danger");
  
  //adding js for error filter
  drupal_add_js('
    function reloadErrors() {
      var types = "";
      if(jQuery("#mh_error_info").get(0).checked) {
        types += "0,";
      }
      if(jQuery("#mh_error_warning").get(0).checked) {
        types += "1,";
      }
      if(jQuery("#mh_error_error").get(0).checked) {
        types += "2,";
      }
      if(jQuery("#mh_error_fatal").get(0).checked) {
        types += "3,";
      }

      types = types.substring(0, types.length - 1);
      var groups = jQuery("#mh_groups").get(0).value;
      var archives = jQuery("#mh_archives").get(0).value;
      var compilers = jQuery("#mh_compilers").get(0).value;
      var path = "/mh/common-errors?"
      if (types != "") {
        path += "types=" + types + "&";
      }
      if (compilers != "") {
        path += "compilers=" + compilers + "&";
      }
      if (groups != "") {
        path += "groups=" + groups + "&";
      }
      if (archives != "") {
        path += "archives=" + archives + "&";
      }
      path = path.substring(0, path.length -1);
      //console.log(path);
      window.location = path;
    }
    ', 'inline');
  $out = '<div>'; //main div
  //adding err filter and statistics accordion
  $out .= '<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">';
  $out .= '<div class="panel panel-default">

   <div class="panel-heading" role="tab" id="filterHeading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#filterBody" aria-expanded="false" aria-controls="filterBody">
          Filter
        </a>
      </h4>
    </div>
<div id="filterBody" class="panel-collapse collapse" role="tabpanel" aria-labelledby="filterHeading">
  <div class="panel-body">
    <div class="form-group">
    <label for="mh_levels"> Error Levels </label> 
    <div>
      <label class="checkbox-inline"><input type="checkbox" id="mh_error_info" ' .  err_lvl_str(0, $err_checked, "checked") . '> Info </label>
      <label class="checkbox-inline"><input type="checkbox" id="mh_error_warning" ' .  err_lvl_str(1, $err_checked, "checked") . '> Warning </label>
      <label class="checkbox-inline"><input type="checkbox" id="mh_error_error" ' .  err_lvl_str(2, $err_checked, "checked") . '> Error </label>
      <label class="checkbox-inline"><input type="checkbox" id="mh_error_fatal" ' .  err_lvl_str(3, $err_checked, "checked") . '> Fatal Error </label>
    </div>
    </div>
    
    <div class="form-group">
      <label for="mh_compilers"> Compilers </label>
      <input type="text" class="form-control" id="mh_compilers" placeholder="Enter Compilers (comma separated)" ' . $err_fields['compilers'] . '>
      <p class="help-block">Leave empty to select all compilers </p>
    </div>
   <div class="form-group">
      <label for="mh_groups"> Libraries </label>
      <input type="text" class="form-control" id="mh_groups" placeholder="Enter Libraries (comma separated)" ' . $err_fields['groups'] . '>
      <p class="help-block">Leave empty to select all libraries </p>
    </div>
    <div class="form-group">
      <label for="mh_archives"> Archives </label>
      <input type="text" class="form-control" id="mh_archives" placeholder="Enter Archives (comma separated)" ' . $err_fields['archives'] . '>  
      <p class="help-block">Leave empty to select all archives </p>
    </div>
    <button class="btn btn-danger" onclick="reloadErrors();">Reload</button>
  </div>
</div>
</div>';
  //statistics
  if (count($results) == 0) {
    drupal_set_message("No errors found, perhaps adjust filter settings");
    return $out . "</div></div>"; //closing accordion and main div
  }
  $out .= '<div class="panel panel-default">';
  $out .= '<div class="panel-heading" role="tab" id="statHeading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#statbody" aria-expanded="false" aria-controls="statbody">
              Statistics 
        </a>
      </h4>
    </div>';  
  $out .= '<div id = "statbody" class="panel-collapse collapse" role="tabpanel" aria-labelledby="statHeading" >';
  $out .= '<div class="panel-body">';
  $out .=  "<h4> <span> Libraries: </span> </h4> <ul> ";
  foreach ($statistics as $group => $gpdata) {
    $out .= '<li><ul class="list-inline"><li><span> ' . $group . ': </span></li>';
    foreach ($gpdata['occurs'] as $type => $occurs) {
      $out .=  '<li ' . err_lvl_str($type, $err_checked,'', 'class="hidden"') . '><span style="color:' . $color_map[$type] . '"> ' . $occurs . ' ' . $name_map[$type] . '(s)</span></li>';
    }
    $out .= "</ul><ul>";
    foreach($gpdata['archives'] as $archive => $archdata) {
      $out .= '<li><ul class="list-inline"><li><span> ' . $archive . ': </span></li>';
      foreach ($archdata as $type => $occurs) {
        $out .=  '<li ' . err_lvl_str($type, $err_checked,'', 'class="hidden"') . '><span style="color:' . $color_map[$type] . '"> ' . $occurs . ' ' . $name_map[$type] . '(s)</span></li>';
      }
      $out .= "</ul></li>";
    }
    $out .= "</ul></li>";
  }
  $out .= "</ul>";
  $out .= '<h4><span "> Compilers: </span></h4> <ul>';     
  foreach ($compilers as $compiler => $types) {
    $out .= '<li><ul class="list-inline"><li><span> ' . $compiler . ': </span></li> ';
    foreach ($types as $type => $msgs) {
      $occurs = $msgs['occurs'];
      $out .=  '<li ' . err_lvl_str($type, $err_checked,'', 'class="hidden"') . '><span style="color:' . $color_map[$type] . '"> ' . $occurs . ' ' . $name_map[$type] . '(s)</span></li>';
    }
    $out .= '</ul></li>';
  }
  $out .= "</ul></div>";
  $out .="</div></div>";
  $out .="</div>";//ending accordion div
  $out .= "<hr/>";
  $pagen = 1; // default page number
  if (!isset($_GET['page']) == "") {
    $pagen = (int)$_GET['page'];  
  }
  $nerrors = count($errors);
  $errperpage = 10; // number of error per page
  $start = ($pagen - 1) * $errperpage;
  $finish = $nerrors;
  if ($finish - $start > $errperpage) {
    $finish = $start + $errperpage;
  }
  $npage = floor($nerrors / $errperpage);
  if ($nerrors % $errperpage != 0) {
    $npage++;
  }
  //starting error list
  $i = 0;
  for ($j = $start; $j < $finish; $j++) {
    $error = $errors[$j];
    $out .= '<div class="node-teaser">';
    $out .= '<h4><span style="color:' . $color_map[$error['type']] . '"> ' . $error['msg'] . '  </span></h4>';
    $out .= '<div class="links"><ul class="list-inline">';
    $out .= '<li><span > ' . $error['compiler'] . ' compiler,</span></li>';
    $out .= '<li><span > ' . $error['occurs'] . ' occurrences, </span></li>';
    $out .= '<li><a style="cursor:pointer;" onclick="if (jQuery(this).html() == \'Show All\') {jQuery(this).html(\'Hide All\')} else {jQuery(this).html(\'Show All\')}; jQuery(\'#oaff_error_log' . $i . '\' ).toggle( \'fold\' );" >Show All</a> </li>';
    if (user_access("administer mathhub")) {
      $eid = $error['eid'];
      $out .= '<a href="/mh/recrawl-error?eid=' . $eid . '" class="btn btn-primary btn-xs" data-toggle="tooltip" data-placement="left" title="Recrawl errors"> <span class="glyphicon glyphicon-refresh"> </span></a> ';      
    }

    $out .= '<div id="oaff_error_log' . $i . '" style="display: none;"><ul>';
    $nids = array_count_values($error['nids']);
    $mtimeRes = db_select('oaff_node_mtime', 'u')
              ->fields('u', array('mtime', 'nid'))
              ->condition('u.nid', array_keys($nids), 'IN')
              ->condition('u.compiler', $error['compiler'])
              ->execute()
              ->fetchAll();
    $mtimes = array();
    foreach ($mtimeRes as $mr) {
      $mtimes[$mr->nid] = $mr->mtime;
    }

    foreach ($nids as $nid => $count) {
      $alias = drupal_lookup_path('alias', 'node/' . $nid);
      //this is probably inefficient and can be optimized
      $title = db_select('node', 'n')
          ->fields('n', array('title'))
          ->condition('n.nid', $nid)
          ->execute()
          ->fetchAssoc()['title'];
      $occurs = $count. ' occurences';
      if ($count == 1) {
        $occurs = $count. ' occurence';
      }
      $mtimeS = '';
      if (isset($mtimes[$nid])) {
        $mtimeS = '<small style="color:gray">' . date('j M G:i', $mtimes[$nid]) . '</small>';
      }

      $out .= '<li><a href="/' . $alias . '"> ' . $title . ' </a> <span>'. $occurs . '</span> ' . $mtimeS . ' </li>';
    }
    $out .= '</ul></div>';
    $out .= '</ul></div>';
    $out .= '</div>';
    $out .= '<hr/>';
    $i += 1;
  }
  // pagination links
  $out .= '
  <nav style="display: table;margin: 0 auto;">
  <ul class="pagination">';
  $pagelink .= 'page=';
  if ($pagen - 1 > 0) {
    $out .= '<li><a href="'.$pagelink.($pagen - 1).'" aria-label="Previous"><span aria-hidden="false">&laquo;</span></a></li>';
  } else {
    $out .= '<li class="disabled"><a href="#" aria-label="Previous"><span aria-hidden="false">&laquo;</span></a></li>';
  }

  for ($j = 1; $j <= $npage; $j++) {
    if ($j == $pagen) {
      $out .= '<li class="active"><a href="'.$pagelink.$j.'">'.$j.'<span class="sr-only">(current)</span></a></li>';
    } else {
      $out .= '<li><a href="'.$pagelink.$j.'">'.$j.'<span class="sr-only">(current)</span></a></li>';
    }
  }

  if ($pagen < $npage) {
    $out .= '<li><a href="'.$pagelink.($pagen + 1).'" aria-label="Previous"><span aria-hidden="true">&raquo;</span></a></li>';
  } else {
    $out .= '<li class="disabled"><a href="#" aria-label="Previous"><span aria-hidden="true">&raquo;</span></a></li>';
  }
  $out .= '</ul>
  </nav>';
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

function oaff_features_recrawl_errors() {
  if (isset($_GET['eid'])) {
    $rerun_nids = array();
    $paths=array();
    $eid = $_GET['eid'];
    $result = db_select('oaff_errors', 'e')
      ->fields('e', array('short_msg'))
      ->condition('eid', $eid)
      ->execute()
      ->fetchAssoc();
    $error_msg = $result['short_msg'];
    $rerun_nids = db_select('oaff_errors', 'e')
      ->fields('e', array('nid'))
      ->condition('short_msg', $error_msg, '=')
      ->execute()
      ->fetchAllAssoc('nid', PDO::FETCH_ASSOC);
    foreach ($rerun_nids as $nid => $value) {
        $node = node_load($nid);
        node_view($node);
    }
    $count = count($rerun_nids);
    drupal_set_message("Re-crawled errors in $count nodes.");    
    drupal_goto('mh/common-errors');
  } else {
    drupal_set_message("No error given (to recrawl)", "warning");
  }
}