<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

if (!(file_exists (($ar = BASEPATH . 'model' . DIRECTORY_SEPARATOR) . 'ActiveRecord.php') && ($database = Config::get ('database'))))
  return false;

_r ('ActiveRecord', $ar);

ActiveRecord\Config::initialize (function ($cfg) use ($database) {
  $cfg->set_model_directory (APPPATH . 'models');
  $cfg->set_connections (array_combine (array_keys ($database['groups']), array_map (function ($group) { return $group['dbdriver'] . '://' . $group['username'] . ':' . $group['password'] . '@' . $group['hostname'] . '/' . $group['database'] . '?charset=' . $group['char_set']; }, $database['groups'])), $database['active_group']);
});

class Model extends ActiveRecord\Model {
  public function __construct () { parent::__construct (); }
}
