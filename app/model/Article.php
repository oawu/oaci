<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2017, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class Article extends Model {
  static $table_name = 'articles';

  static $belongs_to = array (
    // array ('user', 'class_name' => 'User'),
  );
  static $has_many = array (
    array ('comments', 'class_name' => 'Comment', 'include' => array ('user')),
    // array ('comment_users', 'class_name' => 'User', 'from' => 'comments'),
  );

  public function __construct ($attrs = array (), $guardAttrs = true, $instantiatingViafind = false, $newRecord = true) {
    parent::__construct ($attrs, $guardAttrs, $instantiatingViafind, $newRecord);
    Uploader::bind ('cover', 'ArticleCoverImageUploader');
  }
}

class ArticleCoverImageUploader extends ImageUploader {
  public function getVersions () {
    return array (
        '' => array (),
        'c450x180' => array ('adaptiveResizeQuadrant', 450, 180, 'c'),
        'c1200x630' => array ('adaptiveResizeQuadrant', 1200, 630, 't'),
      );
  }
}