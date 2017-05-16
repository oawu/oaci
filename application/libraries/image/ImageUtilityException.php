<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class ImageUtilityException extends Exception {
  private $messages = array ();

  public function __construct () {
    $this->messages = array_2d_to_1d (func_get_args ());
  }
  // return array
  public function getMessages () {
    return $this->messages;
  }
}