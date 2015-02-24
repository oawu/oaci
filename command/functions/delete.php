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

    $model_path = FCPATH . 'application/models/' . ucfirst (camelize ($name)) . EXT;
    $content = read_file ($model_path);

    preg_match_all ('/OrmImageUploader::bind\s*\((?P<k>.*)\);/', $content, $uploaders);

    $uploaders = array_map (function ($uploader) use ($name, $uploader_class_suffix) {
      return isset ($uploader[1]) ? $uploader[1] : ucfirst (camelize ($name)) . $uploader_class_suffix;
    }, array_map (function ($uploader) {
      $pattern = '/(["\'])(?P<kv>(?>[^"\'\\\]++|\\\.|(?!\1)["\'])*)\1?/';
      preg_match_all ($pattern, $uploader, $uploaders);
      return $uploaders['kv'];
    }, $uploaders['k']));

    $uploaders_path = FCPATH . 'application/third_party/orm_image_uploaders/';

    array_map (function ($uploader) use ($uploaders_path) {
      @unlink ($uploaders_path . $uploader . EXT);
    }, $uploaders);

    @unlink ($model_path);
  }
}

if (!function_exists ('delete_cell')) {
  function delete_cell ($name) {
    $name = strtolower ($name);
    $class_suffix  = '_cells';

    $controller_path = FCPATH . 'application/cell/controllers/' . $name . $class_suffix . EXT;
    $views_path = FCPATH . 'application/cell/views/' . $name . $class_suffix . '/';

    @directory_delete ($views_path);
    @unlink ($controller_path);
  }
}