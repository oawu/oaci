<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2014 OA Wu Design
 */
class Main_cells extends Cell_Controller {

  public function _cache_index () {
    return array ('time' => 60 * 60, 'key' => null);
  }
  public function index () {
    return $this->load_view ();
  }
}