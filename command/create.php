<?php

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2015 OA Wu Design
 */

  define ('EXT', '.php');
  define ('SELF', pathinfo (__FILE__, PATHINFO_BASENAME));
  define ('FCPATH', dirname (str_replace (SELF, '', __FILE__)) . '/');

  include 'functions/create.php';

  //       file     type         name              action
  // =============================================================
  // php   create   controller   controller_name   [site | admin | delay]
  // php   create   model        model_name        [(-p | -pic) column_name1, column_name2...]
  // php   create   migration    table_name        [(-a | -add) | (-e | -edit) | (-d | -delete | -del | -drop)]
  // php   create   cell         cell_name         [method_name1, method_name2...]
  // php   create   demo

  $file   = array_shift ($argv);
  $type   = array_shift ($argv);
  $name   = array_shift ($argv);
  $action = array_shift ($argv);
  $temp_path = FCPATH . 'command/templates/create/';

  switch (strtolower ($type)) {
    case 'controller':
      $results = create_controller ($temp_path, $name, $action);
      break;

    case 'model':
      $results = create_model ($temp_path, $name, (($action == '-p') || ($action == '-pic')) && $argv ? $argv : array ());
      break;

    case 'migration':
      $results = create_migration ($temp_path, $name, $action);
      break;

    case 'cell':
      $results = create_cell ($temp_path, $name, array_merge (array ($action), $argv));
      break;

    case 'demo':
      include 'functions/demo.php';
      $results = create_demo ();
      break;
  }
  if (strtolower ($type) != 'demo')
    $results = array_map (function ($result) { $count = 1; return color ('Create: ', 'g') . str_replace (FCPATH, '', $result, $count); }, $results);

  array_unshift ($results, '新增成功!');
  call_user_func_array ('console_log', $results);

