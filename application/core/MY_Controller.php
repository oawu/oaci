<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

class Controller extends CI_Controller {

  public function __construct () {
    parent::__construct ();

    $this->loadOrm ();
  }
  private function loadOrm () {
    if (!(file_exists ($ar = FCPATH . SYSDIR . DIRECTORY_SEPARATOR . 'orm' . DIRECTORY_SEPARATOR . 'ActiveRecord.php') && file_exists ($db = APPPATH . 'config' . DIRECTORY_SEPARATOR . 'database.php') && file_exists ($mm = APPPATH . 'core' . DIRECTORY_SEPARATOR . 'MY_Model.php')))
      return false;

    include $ar;
    $database = include ($db);

    ActiveRecord\Config::initialize (function($cfg) use ($database) {
      $cfg->set_model_directory (APPPATH . 'models');
      $cfg->set_connections (array_combine (array_keys ($database['groups']), array_map (function ($group) { return $group['dbdriver'] . '://' . $group['username'] . ':' . $group['password'] . '@' . $group['hostname'] . '/' . $group['database'] . '?charset=' . $group['char_set']; }, $database['groups'])), $database['active_group']);
    });

    include $mm;
  }
}
