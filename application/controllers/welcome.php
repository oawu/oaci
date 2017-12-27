<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

class welcome extends Controller {

	public function index () {
		echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
		var_dump ($this, round(memory_get_usage() / 1024 / 1024, 2).'MB');
		exit ();

		$this->load->view ('welcome_message');
	}
}
