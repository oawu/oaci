<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class CkeditorImageNameImageUploader extends OrmImageUploader {

  public function getVersions () {
    return array (
        '' => array (),
        '200h' => array ('resize', 200, 200, 'height'),
        '800h' => array ('resize', 800, 800, 'height'),
      );
  }
}