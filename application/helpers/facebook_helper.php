<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */
if (!function_exists ('fb_get_login_url')) {
  function facebook () {
    $CI =& get_instance ();
    if (!isset ($CI->fb))
      $CI->load->library ('fb');
    return $CI->fb;
  }
}
