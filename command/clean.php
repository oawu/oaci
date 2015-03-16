<?php

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2015 OA Wu Design
 */

  include_once 'base.php';
  include_once 'functions/clean.php';

  //       file     type         name                               action
  // =======================================================================
  // php   clean    cache        [cell | file | [assets | static]]

  $file = array_shift ($argv);
  $type = array_shift ($argv);
  $name = array_shift ($argv);

  switch (strtolower ($type)) {
    case 'cache':
      $results = clean_cache ($name);
      break;

    default:
      return console_error ('指令錯誤!', '只接受 cache 四種指令。');
  }


  array_unshift ($results, '清除成功!');
  call_user_func_array ('console_log', $results);