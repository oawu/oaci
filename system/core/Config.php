<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

class Config {
  private static $files = array ();

  public static function init () {
    Config::get ('defines');
  }
  
  public static function get () {
    if (!(($args = func_get_args ()) && ($fileName =  array_shift ($args)))) return null;

    if (!isset (self::$files[$fileName]))
      self::$files[$fileName] = file_exists ($path = APPPATH . 'config' . DIRECTORY_SEPARATOR . ENVIRONMENT . DIRECTORY_SEPARATOR . $fileName . '.php') ? include ($path) : (file_exists ($path = APPPATH . 'config' . DIRECTORY_SEPARATOR . $fileName . '.php') ? include ($path) : null);

    $t = self::$files[$fileName];

    foreach ($args as $arg) {
      if (isset ($t[$arg])) $t = $t[$arg];
      else $t = null;
      if (!$t) break;
    }

    return $t;
  }
}
