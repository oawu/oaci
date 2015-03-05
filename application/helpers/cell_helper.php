<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2015 OA Wu Design
 */

if (!function_exists ('render_cell')) {
  function render_cell () {
    if (count ($params = func_get_args ()) > 1) {
      $CI =& get_instance ();
      if (!isset ($CI->cell)) $CI->load->library ('cell');
      return $CI->cell->render_cell (strtolower (array_shift ($params)), array_shift ($params), $params);
    } else { show_error ('The render_cell params error!'); }
  }
}

if (!function_exists ('clean_cell')) {
  function clean_cell () {
    $CI =& get_instance ();
    if (!isset ($CI->cell)) $CI->load->library ('cell');
    return $CI->cell->clean_cell (func_get_args ());
  }
}
