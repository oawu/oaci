<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2015 OA Wu Design
 */
class Main_cell extends Cell_Controller {

  public function _cache_index () {
    return array ('time' => 5, 'key' => array ('user_id' . 5, '123'));
  }
  public function index () {
    return $this->load_view ();
  }
  public function _cache_type ($a = 0) {
    return array ('time' => 60 * 60, 'key' => $a ? array ('user_id' . $a) : null);
  }
  public function type ($a = 0) {
    return 'sss';
  }
  public function _cache_test () {
    return array ('time' => 60 * 60, 'key' => array ());
  }
  public function test () {
    return 'wwww';
  }
}