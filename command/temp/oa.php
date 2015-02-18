<?php
// $commands = $argv;

define ('SELF', pathinfo (__FILE__, PATHINFO_BASENAME));
define ('FCPATH', str_replace (SELF, '', __FILE__));

include 'oa/oa_functions.php';

$oa = array_shift ($argv);
$file = array_shift ($argv);
$action = array_shift ($argv);
$end = array_shift ($argv);
$name = array_shift ($argv);

switch ($file) {
  case 'controller':
    switch ($action) {
      case 'c':
      case 'create':
        create_controller ($end, $name);
        break;

      case 'd':
      case 'delete':
        delete_controller ($name);
        break;
    }
    break;

  default:
    # code...
    break;
}
