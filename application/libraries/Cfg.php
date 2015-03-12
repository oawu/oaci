<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2015 OA Wu Design
 */

class X {
  private static $config_path = array ('application', 'config', 'system', 'x.php');

  public function __construct () {

  }

  public static function __callStatic ($name, $args) {
    $is_cache = preg_match ("|^(_)|", $name) && ($name = preg_replace ("|^(_)|", '', $name)) ? false : true;
    return self::config ($args, $name, $is_cache);
  }

  private static function config ($args, $forder = 'setting', $is_cache = true) {
    if (!$args)
      return null;

    if (!($configs = (include_once FCPATH . implode (DIRECTORY_SEPARATOR, self::$config_path))))
      return null;

    switch ($configs['driver']) {
      case 'redis':
        if (!class_exists ('CI_Redis'))
          include_once 'Redis.php';

        $redis = new CI_Redis ();
        array_unshift ($args, 'cache', 'system');

        echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
        var_dump ($args);
        exit ();
        break;

      default:
        return null;
        break;
    }
echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
var_dump ($redis);
exit ();
    // if ($levels = array_filter ($arguments)) {

    //   $key = '_config_' . $forder . '_|_' . implode ('_|_', $levels);

    //   if ($is_cache && ($CI =& get_instance ()) && !isset ($CI->cache))
    //     $CI->load->driver ('cache', array ('adapter' => 'apc', 'backup' => 'file'));

    //   if ((!$is_cache || !($data = $CI->cache->file->get ($key))) && ($config_name = array_shift ($levels)) && is_readable ($path = utilitySameLevelPath (FCPATH . APPPATH . 'config' . DIRECTORY_SEPARATOR . $forder . DIRECTORY_SEPARATOR . $config_name . EXT))) {
    //     include $path;
    //     $data = ($config_name = $$config_name) ? _config_recursive ($levels, $config_name) : null;
    //     $is_cache && $CI->cache->file->save ($key, $data, 60 * 60);
    //   }
    // }
    return $data;
  }


}






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