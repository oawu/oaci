<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Main extends Admin_controller {

  public function index () {
    return $this->add_param ('_url', base_url ('admin'))
                ->load_view ();
  }
}
