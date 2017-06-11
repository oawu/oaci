<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Article extends OaModel {

  static $table_name = 'articles';

  static $has_one = array (
  );

  static $has_many = array (
    array ('mappings', 'class_name' => 'ArticleTagMapping'),
    array ('tags', 'class_name' => 'ArticleTag', 'through' => 'mappings'),
    array ('sources', 'class_name' => 'ArticleSource', 'order' => 'sort ASC'),
    array ('images', 'class_name' => 'ArticleImage')
  );

  static $belongs_to = array (
    array ('user', 'class_name' => 'User'),
  );

  const STATUS_1 = 1;
  const STATUS_2 = 2;

  static $statusNames = array (
    self::STATUS_1 => '下架',
    self::STATUS_2 => '上架',
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);

    OrmImageUploader::bind ('cover', 'ArticleCoverImageUploader');
  }
  public function mini_content ($length = 100) {
    if (!isset ($this->content)) return '';
    return $length ? mb_strimwidth (remove_ckedit_tag ($this->content), 0, $length, '…','UTF-8') : remove_ckedit_tag ($this->content);
  }
  public function destroy () {
    if ($this->mappings)
      foreach ($this->mappings as $mapping)
        if (!$mapping->destroy ())
          return false;

    if ($this->sources)
      foreach ($this->sources as $source)
        if (!$source->destroy ())
          return false;

    if ($this->images)
      foreach ($this->images as $image)
        if (!$image->destroy ())
          return false;

    return $this->delete ();
  }
}