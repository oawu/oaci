<?php

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2015 OA Wu Design
 */
include 'public_functions.php';

if (!function_exists ('create_controller')) {
  function create_controller ($end, $name) {
    $controllers_path = FCPATH . 'application/controllers/' . ($end != 'site' ? $end . '/': '');
    $views_path = FCPATH . 'application/views/';

    if (is_writable ($controllers_path) && is_writable ($views_path) && is_writable ($content_path = $views_path . 'content/' . $end . '/')) {
echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
var_dump (EXT);
exit ();
      $a = "<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');\n" . load_view ('views/controller.php', array ('end' => $end, 'name' => $name));
      // write_file ($controllers_path . $name . , $a);
      // echo $controllers_path . ($end == 'admin' ? 'admin/': '');
    //   $oldmask = umask (0);
    //   @mkdir ($content_path . $name, 0777, true);
    //   umask ($oldmask);
    }
  }
}

if (!function_exists ('delete_controller')) {
  function delete_controller ($name) {

  }
}