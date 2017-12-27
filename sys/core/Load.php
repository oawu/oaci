<?php defined ('BASEPATH') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2017, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

if (!function_exists ('_i_r')) {
  function _i_r ($path, $notExistsExit = false, $class = null) {
    static $_path;

    if (isset ($_path[$path]) && $_path[$path])
      return true;

    if (!file_exists ($path) || ((include_once ($path)) === false))
      if ($notExistsExit) exit ((!is_bool ($notExistsExit) ? $notExistsExit : '初始化失敗！') . ' 路徑：' . $path);
      else return false;

    // is_callable ($class) ? $class () : 
    $class === null || eval ($class);

    return $_path[$path] = true;
  }
}

class Load {

  public static function file ($file, $notExistsExit = false, $class = null) {
    return _i_r ($file, $notExistsExit, $class);
  }
  public static function sysCore ($file, $notExistsExit = false, $class = null) {
    return self::file (BASEPATH . 'core' . DIRECTORY_SEPARATOR . $file, $notExistsExit, $class);
  }
  public static function sysFunc ($helper, $notExistsExit = false) {
    return self::file (BASEPATH . 'func' . DIRECTORY_SEPARATOR . $helper, $notExistsExit, null);
  }
  public static function sysLib ($filename, $notExistsExit = false, $class = null) {
    return self::file (BASEPATH . 'lib' . DIRECTORY_SEPARATOR . $filename, $notExistsExit, $class);
  }
  public static function lib ($filename, $notExistsExit = false, $class = null) {
    return self::file (APPPATH . 'lib' . DIRECTORY_SEPARATOR . $filename, $notExistsExit, $class);
  }
}