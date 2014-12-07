<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2014 OA Wu Design
 */
class UserUploader extends OrmImageUploader {

  public function d4_url () {
    return 'https://graph.facebook.com/100000100541088/picture?width=100&height=100';
  }

  public function getVersions () {
    return array (
            '' => array (),
            '30x30' => array ('resize', 30, 30, 'width'),
            '50x50' => array ('resize', 50, 50, 'width'),
            '80x80' => array ('resize', 80, 80, 'width'),
            '100x100' => array ('resize', 100, 100, 'width'),
          );
  }
}