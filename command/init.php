<?php

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2015 OA Wu Design
 */

  include 'base.php';
  include 'functions/create.php';

  //       file       username       password       hostname
  // =============================================================
  // php   init       [root          [password      [127.0.0.1]]]

  $file     = array_shift ($argv);
  $username = ($username = array_shift ($argv)) ? $username : 'root';
  $password = ($password = array_shift ($argv)) ? $password : 'password';
  $hostname = ($hostname = array_shift ($argv)) ? $hostname : '127.0.0.1';
  $temp_path = FCPATH . 'command/templates/init/';

  $date = load_view ($temp_path . 'database.php', array ('hostname' => $hostname, 'username' => $username, 'password' => $password));
  if (!is_writable ($config_path = FCPATH . 'application/config/'))
    console_error ("無法有寫入的權限!");

  if (!write_file ($datebase_path = $config_path . 'database.php', $date))
    console_error ("寫入資料庫設定失敗!");


//cp resource/share/database.php application/config/database.php &&
// mkdir assets &&
// mkdir temp &&
// mkdir upload &&
// mkdir application/cell/cache &&
// mkdir application/cache/file &&
// mkdir application/cache/output &&
// touch application/logs/query-log.log &&
// touch application/logs/delay_job-log.log &&
// chmod 777 assets &&
// chmod 777 temp &&
// chmod 777 upload &&
// chmod 777 application/cell/cache &&
// chmod 777 application/cache/file &&
// chmod 777 application/cache/output &&
// chmod 777 application/logs/query-log.log &&
// chmod 777 application/logs/delay_job-log.log &&
// vi application/config/database.php

