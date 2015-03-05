<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2015 OA Wu Design
 */

class Main extends Site_controller {

  public function __construct () {
    parent::__construct ();
  }

  public function r () {
    // $this->load->library ('redis');
    // echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
    // var_dump ($this->redis->keys ('*'));
    // exit ();;
    clean_cell ('main_cell', 'type', 'user_id1');
    // $this->redis->hmset ('name', 'key1', 'value1', 'key2', 'value2');
    // $this->redis->hsetnx ('name', 'key1', 'value2');


    // $array = $this->redis->hGetArray ('key_1');
  }

  public function index () {
    $this->load_view (null);
    // $this->load_view (null, false, 10);
  }

  public function a () {
    // $this->output->delete_cache ('main');
    // $this->output->delete_all_cache ();
  }

  public function b () {
    // return $this->output_json (array ('status' => true), 100);
  }

  public function delay () {
    delay_job ('main', 'index', array ('sec' => 5));
  }
}
