<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2017, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class View {
  private $path = '';
  private $vals = array ();

  public function __construct ($path = null) {
    $this->path = $path;
    $this->vals = array ();
  }

  public function with ($key, $val = null) {
    if (!$key)
      return $this;

    is_array ($key) && $this->vals = array_merge ($this->vals, $key);
    is_string ($key) && $this->vals[$key] = $val;
    
    return $this;
  }

  public function get () {
    return View::load ($this->path, $this->vals, true);
  }

  public function setPath ($path) {
    return $this;
  }
  public function appendVal ($key, $val) {
    return $this->with ($key, $val);
  }

  public function appendParams ($key, $val) {
    return $this->with ($key, $val);
  }

  public function addVal ($key, $val) {
    return $this->with ($key, $val);
  }

  public function addParams ($key, $val) {
    return $this->with ($key, $val);
  }

  public static function create ($path = null) {
    return new View ($path);
  }

  public static function load ($_x_oa_x_path, $_x_oa_x_params = array (), $_x_oa_x_return = false) {
    ($_x_oa_x_path = ltrim ($_x_oa_x_path, DIRECTORY_SEPARATOR)) && file_exists ($_x_oa_x_path = VIEWPATH . $_x_oa_x_path) || gg ('無法載入 View：' . $_x_oa_x_path);

    extract ($_x_oa_x_params);
    ob_start ();

    @include ($_x_oa_x_path) || gg ('無法載入 View：' . $_x_oa_x_path);

    $buffer = ob_get_contents ();
    @ob_end_clean ();

    return $_x_oa_x_return ? $buffer : Output::appendOutput ($buffer);
  }
}