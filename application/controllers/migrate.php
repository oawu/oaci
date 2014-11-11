<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2014 OA Wu Design
 */
class Migrate extends CI_Controller {

  function __construct () {
    parent::__construct ();
    $this->load->library ("migration");    
  }

  public function update () {
    if (!$this->migration->latest ()) if (!$this->migration->current ()) show_error ($this->migration->error_string ());
  }

  public function rollback ($version) {
    if (!$this->migration->version($version)) show_error($this->migration->error_string());
  }
}