<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2015 OA Wu Design
 */

class OrmFileUploader extends OrmUploader {
  private $configs = array ();

  public function __construct ($orm = null, $column_name = null) {
    parent::__construct ($orm, $column_name);

    $this->configs = Cfg::system ('orm_uploader', 'file_uploader');
  }
  // return string
  public function url () {
    return parent::url ('');
  }
  // return array
  public function path () {
    return parent::path ((string)$this);
  }
}
