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

  $temp_path = FCPATH . 'command/templates/demo/event/';
  $name = 'event';
  $action = 'add';
  create_migration ($temp_path, $name, $action);

  $temp_path = FCPATH . 'command/templates/demo/attendee/';
  $name = 'attendee';
  $action = 'add';
  create_migration ($temp_path, $name, $action);

  $temp_path = FCPATH . 'command/templates/demo/tag/';
  $name = 'tag';
  $action = 'add';
  create_migration ($temp_path, $name, $action);

  $temp_path = FCPATH . 'command/templates/demo/tag_event_map/';
  $name = 'tag_event_map';
  $action = 'add';
  create_migration ($temp_path, $name, $action);



  $temp_path = FCPATH . 'command/templates/demo/event/';
  $name = 'event';
  $action = array ('cover');
  create_model ($temp_path, $name, $action);

  $temp_path = FCPATH . 'command/templates/demo/attendee/';
  $name = 'attendee';
  $action = array ();
  create_model ($temp_path, $name, $action);

  $temp_path = FCPATH . 'command/templates/demo/tag/';
  $name = 'tag';
  $action = array ();
  create_model ($temp_path, $name, $action);

  $temp_path = FCPATH . 'command/templates/demo/tag_event_map/';
  $name = 'tag_event_map';
  $action = array ();
  create_model ($temp_path, $name, $action);


// ------------------------------------------------------------------------------------------------------------


  $temp_path = FCPATH . 'command/templates/demo/cell/';
  $name = 'demo';
  $action = array ('main_menu');
  create_cell ($temp_path, $name, $action);


// ------------------------------------------------------------------------------------------------------------


  $temp_path = FCPATH . 'command/templates/demo/event/';
  $name = 'event';
  $action = 'site';
  $results = create_controller ($temp_path, $name, $action, array ('index', 'show', 'add', 'create', 'edit', 'update', 'destroy'));


  $temp_path = FCPATH . 'command/templates/demo/tag/';
  $name = 'tag';
  $action = 'site';
  $results = create_controller ($temp_path, $name, $action, array ('index', 'show', 'add', 'create', 'edit', 'update', 'destroy'));












