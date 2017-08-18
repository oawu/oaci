<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class BannerCoverImageUploader extends OrmImageUploader {

  public function d4Url () {
    return res_url ('res', 'image', 'uploader.jpg');
  }
  public function getVersions () {
    return array (
        '' => array (),
        '800w' => array ('resize', 800, 800, 'width')
      );
  }
}