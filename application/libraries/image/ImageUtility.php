<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2015 OA Wu Design
 */

class ImageUtility {
  private $CI = null;
  private $object = null;
  private $configs = array ();
  private $error = null;

  public function __construct ($file = '', $module = '', $options = array ()) {
    if (!($file && is_readable ($file)))
      return $this->error = array ('ImageUtility 錯誤！', '初始化失敗！', '檔案不可讀取，或者不存在！file: ' . $file, '請檢查建構子參數！');

    $this->CI =& get_instance ();

    $this->configs = Cfg::system ('image_utility');

    if (!(($module = $module ? strtolower ($module) : $this->configs['module']) && in_array ($module, array_keys ($modules = $this->configs['modules']))))
      return $this->error = array ('ImageUtility 錯誤！', '初始化失敗！', '參數 module 錯誤！module：' . $module, '請檢查建構子參數！');

    if (!is_readable ($path = FCPATH . implode (DIRECTORY_SEPARATOR, array_merge ($this->configs['module_path'], array ($modules[$module] . EXT)))))
      return $this->error = array ('ImageUtility 錯誤！', '初始化失敗！', '不可讀的 module，或者檔案不存在，path：' . $path, '請檢查建構子參數！');

    if (!class_exists ($modules[$module]))
      include_once $path;

    $this->object = new $modules[$module] ($file, $options);
    $this->error = null;
  }

  // return array
  public function getError () {
    return $this->error;
  }

  // return ImageBaseUtility
  public function getObject () {
    return $this->object;
  }

  // return ImageBaseUtility
  public static function create ($fileName, $module = null, $options = array ()) {
    $uti = new ImageUtility ($fileName, $module = null, $options);
    return !$uti->getError () ? $uti->getObject () ? $uti->getObject () : null : call_user_func_array ('error', $uti->getError ());
  }













  // public static function make_block9 () {
  //   if (count ($params = func_get_args ()) < 1)
  //     return false;
  //   if (!(($module = Cfg::system ('image_utility', 'module')) && in_array ($module, array_keys ($modules = Cfg::system ('image_utility', 'modules')))))
  //     return show_error ("The file name or module select error, please confirm your program again.");

  //   $CI =& get_instance ();
  //   if (!is_readable ($path = utilitySameLevelPath (FCPATH . APPPATH . DIRECTORY_SEPARATOR . implode (DIRECTORY_SEPARATOR, $CI->get_libraries_path ()) . DIRECTORY_SEPARATOR . $modules[$module] . EXT)))
  //     return show_error ("The image utility class array format is error!<br/>It must be 'gd' or 'imgk'!<br/>Please confirm your program again.");

  //   if (!class_exists ($modules[$module]))
  //     require_once $path;
  //   return call_user_func_array (array ($modules[$module], 'make_block9'), $params);
  // }
}
