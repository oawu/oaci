<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2015 OA Wu Design
 */
if (!class_exists ('CI_Redis'))
  include_once 'Redis.php';

class X {
  private static $config_path = array ('application', 'config', 'system', 'x.php');
  private static $configs = array ();

  public function __construct () {

  }
  public static function getConfigs () {
    return self::$configs;
  }
  public static function init () {
    self::$configs = (include_once FCPATH . implode (DIRECTORY_SEPARATOR, self::$config_path));
    // load all
  }
  public static function __callStatic ($name, $args) {
    $is_cache = preg_match ("|^(_)|", $name) && ($name = preg_replace ("|^(_)|", '', $name)) ? false : true;
    array_unshift ($args, $name);

    return self::_config ($args, $is_cache);
  }

  private static function _recursive ($data, $args) {
    if (!$args)
      return $data;
    $key = array_shift ($args);

    if (isset ($data[$key]))
      if ($args)
        return self::_recursive ($data[$key], $args);
      else
        return $data[$key];
    else
      return null;
  }
  private static function _load ($args) {
    if (count ($args) < 2)
      return null;

    $data = include FCPATH . implode (DIRECTORY_SEPARATOR, array_merge (self::$configs['directory'], array (array_shift ($args), array_shift ($args) . EXT)));
    return self::_recursive ($data, $args);
  }
  private static function _config ($args, $is_cache = true) {
    if (!$args)
      return null;

    if (!(self::$configs))
      return null;

    switch (self::$configs['driver']) {
      case 'redis':
        $redis = new CI_Redis ();
        $key = array_filter (array_merge (self::$configs['redis']['keys_prefix'], $args));
        $key = implode (':', $args);

        // if ($redis->exists ($key))
        //   $value = unserialize ($redis->get ($key));
        // else
        //   $redis->set ($key, serialize ($value = self::_load ($args)));
        $value = self::_load ($args);
        return $value;
        break;

      default:
        return null;
        break;
    }
    return null;
  }


}
if (!X::getConfigs ())
  X::init ();






// class Cfg {

//   public function __construct () {

//   }

//   public static function __callStatic ($name, $arguments) {
//     if (!function_exists ('config') && ($CI =& get_instance ()))
//       $CI->load->helper ('oa');

//     $is_cache = preg_match ("|^(_)|", $name) && ($name = preg_replace ("|^(_)|", '', $name)) ? false : true;

//     return self::config ($arguments, $name, $is_cache);
//   }

//   private static function config ($arguments, $forder = 'setting', $is_cache = true) {
//     $data = null;

//     if ($levels = array_filter ($arguments)) {

//       $key = '_config_' . $forder . '_|_' . implode ('_|_', $levels);

//       if ($is_cache && ($CI =& get_instance ()) && !isset ($CI->cache))
//         $CI->load->driver ('cache', array ('adapter' => 'apc', 'backup' => 'file'));

//       if ((!$is_cache || !($data = $CI->cache->file->get ($key))) && ($config_name = array_shift ($levels)) && is_readable ($path = utilitySameLevelPath (FCPATH . APPPATH . 'config' . DIRECTORY_SEPARATOR . $forder . DIRECTORY_SEPARATOR . $config_name . EXT))) {
//         include $path;
//         $data = ($config_name = $$config_name) ? _config_recursive ($levels, $config_name) : null;
//         $is_cache && $CI->cache->file->save ($key, $data, 60 * 60);
//       }
//     }
//     return $data;
//   }
// }


class Cfg {
  public function __construct () {
  }

  public static function __callStatic ($name, $arguments) {
    if (!function_exists ('config') && ($CI =& get_instance ()))
      $CI->load->helper ('oa');

    $is_cache = preg_match ("|^(_)|", $name) && ($name = preg_replace ("|^(_)|", '', $name)) ? false : true;

    return config ($arguments, $name, $is_cache);
  }
}