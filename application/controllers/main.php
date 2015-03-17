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
         ->add_hidden (array ('id' => 'get_units_url', 'value' => base_url ($this->get_class (), 'get_units')))
         ->load_view (null);
  }

  public function get_units () {
    $this->output_json (array ('next_id' => 0, 'units' => 'dasd'));
  }
}
