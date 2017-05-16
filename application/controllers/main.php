<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

class Main extends Site_controller {

  public function index () {

    $this->load->library ('PttGeter');
    $uri = '/bbs/Gossiping/index.html';
    echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
    print_r (PttGeter::getListAndPrevNextUri ($uri));
    exit ();
  }
}
