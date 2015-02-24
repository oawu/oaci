<?php

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2015 OA Wu Design
 */

include 'functions.php';

if (!function_exists ('create_controller')) {
  function create_controller ($name, $action, $methods = array ('index')) {
    $results = array ();
    $name = strtolower ($name);
    $action = $action ? $action : 'site';

    $controllers_path = FCPATH . 'application/controllers/' . ($action != 'site' ? $action . '/': '');
    $contents_path = FCPATH . 'application/views/content/' . $action . '/';

    $controllers = array_map (function ($t) { return basename ($t, EXT); }, directory_map ($controllers_path, 1));
    $contents = directory_map ($contents_path, 1);

    if (($controllers && in_array ($name, $controllers)) || ($contents && in_array ($name, $contents)))
      console_error ("名稱重複!");

    if (!is_writable ($controllers_path) || !is_writable ($contents_path))
      console_error ("無法有寫入的權限!");

    $date = "<?php" . load_view ('templates/controller.php', array ('name' => $name, 'action' => $action, 'methods' => $methods));

    if (!write_file ($controller_path = $controllers_path . $name . EXT, $date))
      console_error ("新增 controller 失敗!");

    array_push ($results, $controller_path);

    $oldmask = umask (0);
    @mkdir ($view_path = $contents_path . $name . '/', 0777, true);
    umask ($oldmask);

    if (!is_writable ($view_path)) {
      delete_file ($controller_path);
      console_error ("新增 view 失敗!");
    }

    array_map (function ($method) use ($view_path, &$results) {
      $oldmask = umask (0);
      @mkdir ($view_path . $method . '/', 0777, true);
      umask ($oldmask);

      if (!is_writable ($view_path . $method . '/'))
        return null;

      $files = array ('content.css', 'content.scss', 'content.js', 'content.php');
      array_map (function ($file) use ($view_path, $method, &$results) {
        if (write_file ($view_path . $method . '/' . $file, load_view ('templates/' . $file)))
          array_push ($results, $view_path . $method . '/' . $file);
      }, $files);
    }, $methods);

    return $results;
  }
}

if (!function_exists ('create_model')) {
  function create_model ($name, $columns) {
    $results = array ();
    $name = singularize ($name);

    $uploader_class_suffix = 'Uploader';

    $models_path = FCPATH . 'application/models/';
    $models = array_map (function ($t) { return basename ($t, EXT); }, directory_map ($models_path, 1));

    if ($models && in_array (ucfirst ($name), $models))
      console_error ("名稱重複!");

    if (!is_writable ($models_path))
      console_error ("無法有寫入的權限!");

    $uploaders_path = FCPATH . 'application/third_party/orm_image_uploaders/';
    $uploaders = array_map (function ($t) { return basename ($t, EXT); }, directory_map ($uploaders_path, 1));

    if (!is_writable ($uploaders_path))
      console_error ("Uploader 無法有寫入的權限!");

    $columns = array_filter (array_map (function ($column) use ($name, $uploaders_path, $uploaders, $uploader_class_suffix, &$results) {
      $column = strtolower ($column);
      $uploader = ucfirst (camelize ($name)) . ucfirst ($column) . $uploader_class_suffix;

      if (!in_array ($uploader, $uploaders) && write_file ($uploader_path = $uploaders_path . $uploader . EXT, "<?php" . load_view ('templates/uploader.php', array ('name' => $uploader))) && array_push ($results, $uploader_path))
        return $column;
      return null;
    }, $columns));

    $date = "<?php" . load_view ('templates/model.php', array ('name' => $name, 'columns' => $columns));
    if (!write_file ($model_path = $models_path . ucfirst (camelize ($name)) . EXT, $date)) {
      array_map (function ($column) use ($name, $uploaders_path, $uploader_class_suffix) { delete_file ($uploaders_path . ucfirst (camelize ($name)) . ucfirst ($column) . $uploader_class_suffix . EXT); }, $columns);
      console_error ("新增 model 失敗!");
    }

    array_push ($results, $model_path);
    return $results;
  }
}

if (!function_exists ('create_migration')) {
  function create_migration ($name, $action) {
    $results = array ();
    $name = strtolower ($name);

    switch ($action) {
      case '-e': case '-edit':
        $action = 'edit'; break;

      case '-d': case '-delete': case '-del': case '-drop':
        $action = 'drop'; break;

      default:
        $action = 'add'; break;
    }

    $migrations_path = FCPATH . 'application/migrations/';
    $migrations = array_filter (array_map (function ($t) { return '.' . pathinfo ($t, PATHINFO_EXTENSION) == EXT ? basename ($t, EXT) : null; }, directory_map ($migrations_path, 1)));

    if (!is_writable ($migrations_path))
      console_error ("無法有寫入的權限!");

    $count = sprintf ("%03d", max (array_map (function ($migration) { return substr ($migration, 0, strpos ($migration, '_')); }, $migrations)) + 1);

    if ($migrations && in_array ($file_name = $count . '_' . $action . '_' . pluralize ($name), $migrations))
      console_error ("名稱錯誤!");
    else
      $file_name .= EXT;

    $date = "<?php" . load_view ('templates/migration.php', array ('name' => $name, 'action' => $action));

    if (!write_file ($migrations_path . $file_name, $date))
      console_error ("寫檔失敗!");

    array_push ($results, $migrations_path . $file_name);
    return $results;
  }
}

if (!function_exists ('create_cell')) {
  function create_cell ($name, $methods = array ()) {
    $results = array ();
    $name = strtolower ($name);
    $methods = array_filter ($methods);

    $class_suffix  = '_cells';
    $method_prefix = '_cache_';

    $controllers_path = FCPATH . 'application/cell/controllers/';
    $views_path = FCPATH . 'application/cell/views/';

    $controllers = array_map (function ($t) { return basename ($t, EXT); }, directory_map ($controllers_path, 1));
    $views = directory_map ($views_path, 1);

    if (!is_writable ($controllers_path) || !is_writable ($views_path))
      console_error ("無法有寫入的權限!");

    if (($controllers && in_array ($file_name = $name . $class_suffix, $controllers)) || ($views && in_array ($file_name, $views)))
      console_error ("名稱錯誤!");

    $date = "<?php" . load_view ('templates/cell.php', array ('file_name' => $file_name, 'name' => $name, 'methods' => $methods, 'method_prefix' => $method_prefix));

    if (!write_file ($controller_path = $controllers_path . $file_name . EXT, $date))
      console_error ("新增 controller 失敗!");

    array_push ($results, $controller_path);

    $oldmask = umask (0);
    @mkdir ($view_path = $views_path . $file_name . '/', 0777, true);
    umask ($oldmask);

    if (!is_writable ($view_path)) {
      delete_file ($controller_path);
      console_error ("新增 controller 失敗!");
    }

    if (!array_filter (array_map (function ($method) use ($view_path, &$results) { return write_file ($method_path = $view_path . $method . EXT, '') ? array_push ($results, $method_path) : null; }, $methods)) && $methods) {
      @directory_delete ($view_path);
      delete_file ($controller_path);
      console_error ("新增 view 失敗!");
    }
    return $results;
  }
}
