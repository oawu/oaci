<?php

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2015 OA Wu Design
 */

include 'functions.php';

if (!function_exists ('color')) {
  function color ($string, $foreground_color = null, $background_color = null, $is_print = false) {
    if (!strlen ($string)) return "";
    $colored_string = "";
    $keys = array ('n' => '30', 'w' => '37', 'b' => '34', 'g' => '32', 'c' => '36', 'r' => '31', 'p' => '35', 'y' => '33');
    if ($foreground_color && in_array (strtolower ($foreground_color), array_map ('strtolower', array_keys ($keys)))) {
      $foreground_color = !in_array (ord ($foreground_color[0]), array_map ('ord', array_keys ($keys))) ? in_array (ord ($foreground_color[0]) | 0x20, array_map ('ord', array_keys ($keys))) ? '1;' . $keys[strtolower ($foreground_color[0])] : null : $keys[$foreground_color[0]];
      $colored_string .= $foreground_color ? "\033[" . $foreground_color . "m" : "";
    }
    $colored_string .= $background_color && in_array (strtolower ($background_color), array_map ('strtolower', array_keys ($keys))) ? "\033[" . ($keys[strtolower ($background_color[0])] + 10) . "m" : "";

    if (substr ($string, -1) == "\n") { $string = substr ($string, 0, -1); $has_new_line = true; } else { $has_new_line = false; }
    $colored_string .=  $string . "\033[0m";
    $colored_string = $colored_string . ($has_new_line ? "\n" : "");
    if ($is_print) printf ($colored_string);
    return $colored_string;
  }
}

if (!function_exists ('console_log')) {
  function console_log () {
    $messages = array_filter (func_get_args ());
    $db_line = color (str_repeat ('=', 70), 'N') . "\n";
    $line = color (str_repeat ('-', 70), 'w') . "\n";

    echo "\n" .
          $db_line .
          color ('  ERROR!', 'r') . color (" - ", 'R') . color (array_shift ($messages), 'W') . "\n" .
          $db_line;
          $messages = implode ("", array_map (function ($message) {
                                    return color ('  ' . $message, 'w') . "\n";
                                  }, $messages));
    echo $messages ? $messages . $db_line : '';
    echo "\n";
  }
}

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