<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2015 OA Wu Design
 */

class OrmUploader {
  protected $error = null;
  protected $CI = null;
  protected $orm = null;
  protected $column_name = null;
  protected $column_value = null;
  private $configs = array ();

  public function __construct ($orm = null, $column_name = null) {
    if (!($orm && $column_name && in_array ($column_name, array_keys ($orm->attributes ()))))
      return $this->error = array ('OrmUploader 錯誤！', '初始化失敗！', '請檢查建構子參數！');

    $this->CI =& get_instance ();

    $this->orm = $orm;
    $this->column_name = $column_name;
    $this->column_value = $orm->$column_name;
    $orm->$column_name = $this;

    $this->configs = Cfg::system ('orm_uploader', 'uploader');
    $this->error = null;

    if (!in_array ($this->configs['unique_column'], array_keys ($orm->attributes ())))
      return $this->error = array ('OrmUploader 錯誤！', '無法取得 unique 欄位資訊！', '請 ORM select，或者修改 unique 欄位名稱(' . $this->configs['unique_column'] . ')！', '修改 unique 欄位名稱至 config/system/orm_uploader.php 設定檔修改！');
  }

  // return string
  public function url ($key = '') {
    if ($this->error)
      return $this->configs['debug'] ? call_user_func_array ('error', $this->error) : '';

    switch ($this->configs['bucket']) {
      case 'local':
        return ($path = $this->path ($key)) ? base_url ($path) : $this->d4_url ();
        break;
    }

    return $this->configs['debug'] ? error ('OrmUploader 錯誤！', '未知的 bucket，系統尚未支援' . $this->configs['bucket'] . ' 的空間！', '請檢查 config/system/orm_uploader.php 設定檔！') : '';
  }
  // return array
  public function path ($fileName = '') {
    if ($this->error)
      return $this->configs['debug'] ? call_user_func_array ('error', $this->error) : array ();

    switch ($this->configs['bucket']) {
      case 'local':
        if (is_readable (FCPATH . implode(DIRECTORY_SEPARATOR, $path = array_merge ($this->configs['base_directory'][$this->configs['bucket']], $this->getSavePath (), array ($fileName)))))
          return $path;
        else
          return array ();
        break;
    }

    return $this->configs['debug'] ? error ('OrmUploader 錯誤！', '未知的 bucket，系統尚未支援' . $this->configs['bucket'] . ' 的空間！', '請檢查 config/system/orm_uploader.php 設定檔！') : array ();
  }
  // return sring
  protected function d4_url () {
    return $this->configs['d4_url'];
  }
  // return array
  protected function getSavePath () {
    return ($id = $this->getColumnValue ($this->configs['unique_column'])) ? array ($this->getTableName (), $this->getColumnName (), floor ($id / 1000000), floor (($id % 1000000) / 10000), floor ((($id % 1000000) % 10000) / 100), ($id % 100)) : array ($this->getTableName (), $this->getColumnName ());
  }
  // return sring
  protected function getTableName () {
    return $this->orm->table ()->table;
  }
  // return sring
  protected function getColumnName () {
    return $this->column_name;
  }
  // return sring
  protected function getColumnValue ($column_name) {
    return isset ($this->orm->$column_name) ? $this->orm->$column_name : '';
  }
  // return sring
  public function __toString () {
    return  $this->error ? call_user_func_array ('error', $this->error) : (string)$this->column_value;
  }
  // return OrmUploader object
  public static function bind ($column_name, $class_name = null) {
    if (!$column_name)
      return error ('OrmUploader 錯誤！', 'OrmUploader::bind 參數錯誤！', '請確認 OrmUploader::bind 的使用方法的正確性！');

    if (!($trace = debug_backtrace (DEBUG_BACKTRACE_PROVIDE_OBJECT)))
      return error ('OrmUploader 錯誤！', '取得 debug_backtrace 發生錯誤，無法取得 debug_backtrace！', '請確認 OrmUploader::bind 的使用方法的正確性！');

    if (!(isset ($trace[1]['object']) && is_object ($orm = $trace[1]['object'])))
      return error ('OrmUploader 錯誤！', '取得 debug_backtrace 回傳結構有誤，無法取得上層物件！', '請確認 OrmUploader::bind 的使用方法的正確性！');

    if (!$class_name)
      $class_name = get_class ($orm) . Cfg::system ('orm_uploader', 'instance', 'class_suffix');

    if (is_readable ($path = FCPATH . implode (DIRECTORY_SEPARATOR, array_merge (Cfg::system ('orm_uploader', 'uploader', 'instance', 'directory'), array ($class_name . EXT)))))
      require_once $path;
    else
      $class_name = get_called_class ();
    return $object = new $class_name ($orm, $column_name);
  }
}

include_once 'OrmImageUploader.php';

include_once 'OrmFileUploader.php';
