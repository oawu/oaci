<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

// $route['default_controller'] = 'welcome';
// $route['404_override'] = '';
// $route['translate_uri_dashes'] = FALSE;

Router::root ('main');
Router::post ('main/a');
Router::post ('main/b', 'main@a');
Router::get ('main/c', 'main@a');
