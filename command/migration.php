<?php

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2015 OA Wu Design
 */

  //       file       version
  // ==========================================
  // php   migration  [0 | 1 | 2...]

  include 'base.php';
  include 'functions/functions.php';

  $file    = array_shift ($argv);
  $version = array_shift ($argv);

  require (BASEPATH . 'core/Common.php');
  require (FCPATH . 'application/config/constants.php');

  set_error_handler('_exception_handler');

  if (!is_php ('5.3'))
    @set_magic_quotes_runtime (0);

  if (isset ($assign_to_config['subclass_prefix']) && ($assign_to_config['subclass_prefix'] != ''))
    get_config (array('subclass_prefix' => $assign_to_config['subclass_prefix']));

  if ((function_exists ('set_time_limit') == true) && (@ini_get ("safe_mode") == 0))
    @set_time_limit (300);


  $CFG =& load_class ('Config', 'core');

  if (isset ($assign_to_config))
    $CFG->_assign_to_config ($assign_to_config);

  $UNI =& load_class('Utf8', 'core');

  $LANG =& load_class('Lang', 'core');

  require BASEPATH.'core/Controller.php';

  function &get_instance () {
    return CI_Controller::get_instance ();
  }

  $controller = new CI_Controller();
  $controller->load->library ('migration');

  if ($version)
    $controller->migration->latest ();
  else
    $controller->migration->version ($version);
