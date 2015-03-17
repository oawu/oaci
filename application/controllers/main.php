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
    $this->add_js (base_url ('resource', 'javascript', 'jQueryMousewheel_v3.1.12', 'jquery.mousewheel.min.js'))
         ->load_view (null);
  }
}
