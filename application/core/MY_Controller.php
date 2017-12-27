<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

class Controller extends CI_Controller {

  public function __construct () {
    parent::__construct ();

    $this->loadOrm ();
  }
  private function loadOrm () {
    
  }
}
