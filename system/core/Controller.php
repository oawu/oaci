<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

class Controller {
	public function __construct () {
	}
}
spl_autoload_register (function ($class) {
  if (!class_exists ($class) && preg_match ("/Controller$/", $class) && file_exists ($file = APPPATH . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . $class . '.php'))
    require_once $file;
});

