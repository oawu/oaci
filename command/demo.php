<?php

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2015 OA Wu Design
 */

  define ('EXT', '.php');
  define ('SELF', pathinfo (__FILE__, PATHINFO_BASENAME));
  define ('FCPATH', dirname (str_replace (SELF, '', __FILE__)) . '/');
  define ('TEMP_PATH', FCPATH . 'command/templates_demo/');

  include 'functions/create.php';

  $temp_path = FCPATH . 'command/templates/demo/user';

  // $name = 'user';
  // $action = 'add';
  // create_migration ($temp_path, $name, $action, 'migration_user.php');

  // $name = 'user';
  // $action = array ();
  // create_model ($temp_path, $name, $action, );

  // $name = 'demo';
  // $action = 'site';
  // $results = create_controller ($temp_path, $name, $action);

