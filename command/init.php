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


  if (!is_writable ($path_config = FCPATH . 'application/config/'))
    console_error ("無法有 application/config/ 的寫入權限!");

  if (!is_writable ($path_logs = FCPATH . 'application/logs/'))
    console_error ("無法有 application/logs/ 的寫入權限!");


  $oldmask = umask (0);
  @mkdir ($path_assets = FCPATH . 'assets' . '/', 0777, true);
  umask ($oldmask);

  $oldmask = umask (0);
  @mkdir ($path_temp = FCPATH . 'temp' . '/', 0777, true);
  umask ($oldmask);

  $oldmask = umask (0);
  @mkdir ($path_upload = FCPATH . 'upload' . '/', 0777, true);
  umask ($oldmask);

  $oldmask = umask (0);
  @mkdir ($path_cache = FCPATH . 'application/cell/cache' . '/', 0777, true);
  umask ($oldmask);

  $oldmask = umask (0);
  @mkdir ($path_cache_file = FCPATH . 'application/cache/file' . '/', 0777, true);
  umask ($oldmask);

  $oldmask = umask (0);
  @mkdir ($path_cache_output = FCPATH . 'application/cache/output' . '/', 0777, true);
  umask ($oldmask);


  $date = load_view ($temp_path . 'database.php', array ('hostname' => $hostname, 'username' => $username, 'password' => $password));
  if (!write_file ($path_database_php = $path_config . 'database.php', $date))
    console_error ("寫入 database.php 失敗!");
  $oldmask = umask (0);
  chmod($path_database_php, 0777);
  umask ($oldmask);

  $date = load_view ($temp_path . 'query.log');
  if (!write_file ($path_query_log = $path_logs . 'query.log', $date))
    console_error ("寫入 query.log 失敗!");
  $oldmask = umask (0);
  chmod($path_query_log, 0777);
  umask ($oldmask);

  $date = load_view ($temp_path . 'delay_job.log');
  if (!write_file ($path_delay_job_log = $path_logs . 'delay_job.log', $date))
    console_error ("寫入 delay_job.log 失敗!");
  $oldmask = umask (0);
  chmod($path_delay_job_log, 0777);
  umask ($oldmask);