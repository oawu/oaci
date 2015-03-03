<?php

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2015 OA Wu Design
 */

if (!function_exists ('create_demo')) {
  function create_demo () {
    $results = array ();

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

    array_push ($results, 'Migration 新增成功!');

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

    array_push ($results, 'Model 新增成功!');

  // ------------------------------------------------------------------------------------------------------------


    $temp_path = FCPATH . 'command/templates/demo/cell/';
    $name = 'demo';
    $action = array ('main_menu');
    create_cell ($temp_path, $name, $action);

    array_push ($results, 'Cell 新增成功!');

  // ------------------------------------------------------------------------------------------------------------


    $temp_path = FCPATH . 'command/templates/demo/event/';
    $name = 'event';
    $action = 'site';
    create_controller ($temp_path, $name, $action, array ('index', 'show', 'add', 'create', 'edit', 'update', 'destroy'));


    $temp_path = FCPATH . 'command/templates/demo/tag/';
    $name = 'tag';
    $action = 'site';
    create_controller ($temp_path, $name, $action, array ('index', 'show', 'add', 'create', 'edit', 'update', 'destroy'));

    array_push ($results, 'Controller 新增成功!');

    return $results;
  }
}


if (!function_exists ('delete_demo')) {
  function delete_demo () {
    $name = 'event';
    $action = 'site';
    delete_controller ($name, $action);

    $name = 'tag';
    $action = 'site';
    delete_controller ($name, $action);


    $name = 'demo';
    delete_cell ($name);


    $name = 'event';
    delete_model ($name);

    $name = 'attendee';
    delete_model ($name);

    $name = 'tag';
    delete_model ($name);

    $name = 'tag_event_map';
    delete_model ($name);
  }
}