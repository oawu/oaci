<?php

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2015 OA Wu Design
 */

include 'functions.php';

if (!function_exists ('delete_controller')) {
  function delete_controller ($name, $action) {
    $name = strtolower ($name);
    $action = $action ? $action : 'site';

    $controller_path = FCPATH . 'application/controllers/' . ($action != 'site' ? $action . '/': '') . $name . EXT;
    $contents_path = FCPATH . 'application/views/content/' . $action . '/' . $name . '/';

    @directory_delete ($contents_path);
    @unlink ($controller_path);
  }
}

if (!function_exists ('delete_model')) {
  function delete_model ($name) {
    $name = singularize ($name);

    $uploader_class_suffix = 'Uploader';

    $model_path = FCPATH . 'application/models/' . $name;
    echo $model_path;


    // $name = strtolower ($name);
    // $action = $action ? $action : 'site';

    // $controller_path = FCPATH . 'application/controllers/' . ($action != 'site' ? $action . '/': '') . $name . EXT;
    // $contents_path = FCPATH . 'application/views/content/' . $action . '/' . $name . '/';

    // @directory_delete ($contents_path);
    // @unlink ($controller_path);
  }
}