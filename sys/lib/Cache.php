<?php defined ('BASEPATH') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2017, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class Cache {
  private static $drivers = array (
      'file' => null,
      'redis' => null,
      'memcached' => null,
    );

  public static function __callStatic ($method, $args = array ()) {
    if (!(count ($args) > 1 && is_string ($key = array_shift ($args)) && $key))
      return null;

    $function = array_shift ($args);
    $ttl = (($ttl = array_shift ($args)) !== null) && is_numeric ($ttl) ? (int)$ttl : 60;

    if (!in_array ($method, array_keys (self::$drivers)))
      gg ('[Cache] Cache 錯誤，為支援的 Driver 類型。Driver：' . $method);

    self::$drivers[$method] || Load::sysLib ('CacheDrivers' . DIRECTORY_SEPARATOR . ucfirst ($method) . '.php', 'Cache' . ucfirst ($method) . '」Driver 不存在。') && ($class = 'Cache' . ucfirst ($method) . 'Driver') && self::$drivers[$method] = new $class ();

    if (($data = call_user_func_array (array (self::$drivers[$method], 'get'), array ($key))) !== null)
      return $data;

    if (is_callable ($function) && (call_user_func_array (array (self::$drivers[$method], 'save'), array ($key, $function (), $ttl)) || true))
      return $data;

    foreach (array ('is_bool', 'is_numeric', 'is_string', 'is_array', 'is_object') as $check)
      if ($check ($function) && (call_user_func_array (array (self::$drivers[$method], 'save'), array ($key, $function, $ttl)) || true))
        return $function;

    return $function;
  }
}
