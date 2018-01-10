<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

if (!function_exists ('refresh')) {
  function refresh ($url, $key, $data) {
    static $loaded;
    $loaded || $loaded = Load::sysLib ('Session.php', true);
    Session::setFlashData ($key, $data);
    return URL::refresh ($url);
  }
}