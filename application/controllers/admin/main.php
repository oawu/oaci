<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2015 OA Wu Design
 */
class Main extends Admin_controller {
  public function __construct () {
    parent::__construct ();
  }

  public function index () {
    echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
    var_dump ('s');
    exit ();
    $this->load_view ();
  }
}
