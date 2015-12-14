<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2015 OA Wu Design
 */

class Main extends Site_controller {

  public function index () {
    $this->load->library ('CompressorIo');
    CompressorIo::postAndDownload (array ('temp/805597940_566e39c62b4d7.png', 'temp/08.jpg'));
    CompressorIo::postAndDownload ('temp/08.jpg');

    $this->load_view ();
  }
}
