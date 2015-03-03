<?php

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2015 OA Wu Design
 */

if (!function_exists ('create_demo')) {
  function create_demo () {
    $main_results = array ();
    $db_line = color (str_repeat ('=', 80), 'N') . "\n";
    $line = color (str_repeat ('-', 80), 'w') . "\n";

    echo $db_line;
    $results = array ();
    $migrations = array ('event' => array (), 'attendee' => array (), 'tag' => array (), 'tag_event_map' => array ());
    array_walk ($migrations, function ($value, $key) use (&$results) {
      array_push ($results, implode ("\n", array_map (function ($result) { $count = 1; return color ('Create: ', 'g') . str_replace (FCPATH, '', $result, $count); }, create_migration (FCPATH . 'command/templates/demo/' . $key . '/', $key, 'add'))));
    });
    echo implode ("\n", $results) . "\n" . $line;

    $results = array ();
    $models = array ('event' => array ('cover'), 'attendee' => array (), 'tag' => array (), 'tag_event_map' => array ());
    array_walk ($models, function ($value, $key) use (&$results) {
      array_push ($results, implode ("\n", array_map (function ($result) { $count = 1; return color ('Create: ', 'g') . str_replace (FCPATH, '', $result, $count); }, create_model (FCPATH . 'command/templates/demo/' . $key . '/', $key, $value))));
    });
    echo implode ("\n", $results) . "\n" . $line;

    $results = array ();
    $cells = array ('demo' => array ('main_menu'));
    array_walk ($cells, function ($value, $key) use (&$results) {
      array_push ($results, implode ("\n", array_map (function ($result) { $count = 1; return color ('Create: ', 'g') . str_replace (FCPATH, '', $result, $count); }, create_cell (FCPATH . 'command/templates/demo/cell/', $key, $value))));
    });
    echo implode ("\n", $results) . "\n" . $line;


    $results = array ();
    $controllers = array ('event' => array (), 'tag' => array ());
    array_walk ($controllers, function ($value, $key) use (&$results) {
      array_push ($results, implode ("\n", array_map (function ($result) { $count = 1; return color ('Create: ', 'g') . str_replace (FCPATH, '', $result, $count); }, create_controller (FCPATH . 'command/templates/demo/' . $key . '/', $key, 'site', array ('index', 'show', 'add', 'create', 'edit', 'update', 'destroy')))));
    });
    echo implode ("\n", $results) . "\n";

    array_push ($main_results, "migrations(" . implode(', ', array_keys ($migrations)) . ")");
    array_push ($main_results, "models(" . implode(', ', array_keys ($models)) . ")");
    array_push ($main_results, "cells(" . implode(', ', array_keys ($cells)) . ")");
    array_push ($main_results, "controllers(" . implode(', ', array_keys ($controllers)) . ")");

    return $main_results;
  }
}


if (!function_exists ('delete_demo')) {
  function delete_demo () {
    $results = array ();

    array_map (function ($name) {
      delete_controller ($name, 'site');
    }, array ('event', 'tag'));

    array_map (function ($name) {
      delete_cell ($name);
    }, array ('demo'));

    array_map (function ($name) {
      delete_model ($name);
    }, array ('event', 'attendee', 'tag', 'tag_event_map'));

    return $results;
  }
}