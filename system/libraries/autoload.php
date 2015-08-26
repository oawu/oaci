<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2015 OA Wu Design
 */

class Autoload {
  static function __autoload_elastica ($class) {
    if (stripos ($class, 'Elastica') !== FALSE) {
      $path = str_replace ('_', DIRECTORY_SEPARATOR, $class);
      require_once FCPATH . 'system' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . $path . EXT;
    }
  }
}
