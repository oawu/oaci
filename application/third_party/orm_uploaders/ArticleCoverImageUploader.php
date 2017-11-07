<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class ArticleCoverImageUploader extends OrmImageUploader {

  public function d4Url () {
    return res_url ('res', 'image', 'uploader.jpg');
  }
  public function getVersions () {
    return array (
        '' => array (),
        'c450x180' => array ('adaptiveResizeQuadrant', 450, 180, 'c'),
        'c1200x630' => array ('adaptiveResizeQuadrant', 1200, 630, 't'),
      );
  }
}