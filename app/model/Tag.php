<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class Tag extends Model {
  static $table_name = 'tags';

  static $has_one = array (
  );

  static $has_many = array (
    array ('articles')
  );

  static $belongs_to = array (
  );

  const STATUS_ON  = 'on';
  const STATUS_OFF = 'off';

  static $statusNames = array (
    self::STATUS_ON  => '上架',
    self::STATUS_OFF => '下架',
  );

  public function destroy () {
    if (!isset ($this->id))
      return false;

    foreach ($this->articles as $article)
      if (!$article->destroy ())
        return false;
    
    return $this->delete ();
  }
}