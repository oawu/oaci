<?php

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2015 OA Wu Design
 */

  define ('EXT', '.php');
  define ('SELF', pathinfo (__FILE__, PATHINFO_BASENAME));
  define ('FCPATH', dirname (str_replace (SELF, '', __FILE__)) . '/');

  include 'functions/create.php';

// Tag
// TagEventMap
// Event
// Attendee








  // $temp_path = FCPATH . 'command/templates/demo/user/';
  // $name = 'user';
  // $action = 'add';
  // create_migration ($temp_path, $name, $action);

  // $temp_path = FCPATH . 'command/templates/demo/article/';
  // $name = 'article';
  // $action = 'add';
  // create_migration ($temp_path, $name, $action);

  // $temp_path = FCPATH . 'command/templates/demo/tag/';
  // $name = 'tag';
  // $action = 'add';
  // create_migration ($temp_path, $name, $action);

  // $temp_path = FCPATH . 'command/templates/demo/tag_article_map/';
  // $name = 'tag_article_map';
  // $action = 'add';
  // create_migration ($temp_path, $name, $action);

  // $temp_path = FCPATH . 'command/templates/demo/comment/';
  // $name = 'comment';
  // $action = 'add';
  // create_migration ($temp_path, $name, $action);




  // $temp_path = FCPATH . 'command/templates/demo/user/';
  // $name = 'user';
  // $action = array ('avatar');
  // create_model ($temp_path, $name, $action);

  // $temp_path = FCPATH . 'command/templates/demo/article/';
  // $name = 'article';
  // $action = array ();
  // create_model ($temp_path, $name, $action);

  // $temp_path = FCPATH . 'command/templates/demo/tag/';
  // $name = 'tag';
  // $action = array ();
  // create_model ($temp_path, $name, $action);

  // $temp_path = FCPATH . 'command/templates/demo/tag_article_map/';
  // $name = 'tag_article_map';
  // $action = array ();
  // create_model ($temp_path, $name, $action);

  // $temp_path = FCPATH . 'command/templates/demo/comment/';
  // $name = 'comment';
  // $action = array ();
  // create_model ($temp_path, $name, $action);





  // $temp_path = FCPATH . 'command/templates/demo/user/';
  // $name = 'user';
  // $action = 'site';
  // $results = create_controller ($temp_path, $name, $action, array ('index', 'show', 'add', 'create', 'edit', 'update', 'destroy'));

  $temp_path = FCPATH . 'command/templates/demo/user/';
  $name = 'demo';
  $action = array ('main_menu');
  create_cell ($temp_path, $name, $action);











