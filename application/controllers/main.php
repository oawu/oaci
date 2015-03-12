<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2015 OA Wu Design
 */

class Main extends Site_controller {

  public function __construct () {
    parent::__construct ();
  }

  public function index () {
    // echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
    // // var_dump (X::system ('test', 'items'));
    // var_dump (Cfg::system ('o', 'items'));
    // exit ();
    // $this->load_view (null);
    for ($i = 0; $i < 9999; $i++)
      X::system ('test', 'items');
      // Cfg::system ('o', 'items');

    echo "x";
  }
}
