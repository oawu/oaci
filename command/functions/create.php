<?php

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2015 OA Wu Design
 */

include 'functions.php';

if (!function_exists ('create_controller')) {
  function create_controller ($name, $action) {
    $action = $action ? $action : 'site';

    $controllers_path = FCPATH . 'application/controllers/' . ($action != 'site' ? $action . '/': '');
    $contents_path = FCPATH . 'application/views/content/' . $action . '/';

    $controllers = array_map (function ($t) { return basename ($t, EXT); }, directory_map ($controllers_path, 1));
    $contents = directory_map ($contents_path, 1);

    if (($controllers && in_array ($name, $controllers)) || ($contents && in_array ($name, $contents)))
      console_log ("名稱重複!");

    if (!is_writable ($controllers_path) || !is_writable ($contents_path))
      echo "無法有寫入的權限!\n";

    $methods = array ('index');
    $date = "<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');\n\n" . load_view ('templates/controller.php', array ('name' => $name, 'action' => $action, 'methods' => $methods));

    if (write_file ($name_controller_path = $controllers_path . $name . EXT, $date)) {
      $oldmask = umask (0);
      @mkdir ($name_view_path = $contents_path . $name . '/', 0777, true);
      umask ($oldmask);

      if (is_writable ($name_view_path)) {
        if ($methods) {
          foreach ($methods as $method) {
            $oldmask = umask (0);
            @mkdir ($name_view_path . $method . '/', 0777, true);
            umask ($oldmask);

            if (is_writable ($name_view_path . $method . '/')) {
              $files = array ('content.css', 'content.scss', 'content.js', 'content.php');

              if ($files) {
                foreach ($files as $file) {
                  write_file ($name_view_path . $method . '/' . $file, load_view ('templates/' . $file));
                }
              }
            }
          }
        }
      } else {
        @unlink ($name_controller_path);
      }
    } else {
      echo "新增 controller 失敗!\n";
    }
  }
}