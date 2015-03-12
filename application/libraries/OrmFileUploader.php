<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2015 OA Wu Design
 */

class OrmFileUploader {

  // return OrmFileUploader object
  public static function bind ($column_name, $class_name = null) {
    if (!$column_name)
      return error ('OrmFileUploader 錯誤！', 'OrmFileUploader::bind 參數錯誤！', '請確認 OrmFileUploader::bind 的使用方法的正確性！');

    if (!($trace = debug_backtrace (DEBUG_BACKTRACE_PROVIDE_OBJECT)))
      return error ('OrmFileUploader 錯誤！', '取得 debug_backtrace 發生錯誤，無法取得 debug_backtrace！', '請確認 OrmFileUploader::bind 的使用方法的正確性！');

    if (!(isset ($trace[1]['object']) && is_object ($orm = $trace[1]['object'])))
      return error ('OrmFileUploader 錯誤！', '取得 debug_backtrace 回傳結構有誤，無法取得上層物件！', '請確認 OrmFileUploader::bind 的使用方法的正確性！');

    if (!$class_name)
      $class_name = get_class ($orm) . Cfg::system ('orm_file_uploader', 'instance', 'class_suffix');

    if (is_readable ($path = FCPATH . implode (DIRECTORY_SEPARATOR, array_merge (Cfg::system ('orm_file_uploader', 'instance', 'directory'), array ($class_name . EXT)))))
      require_once $path;
    else
      $class_name = get_called_class ();

    return $object = new $class_name ($orm, $column_name);
  }
}