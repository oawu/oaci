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
        // array('product', 'class_name' => 'Product', 'foreign_key' => 'productId', 'primary_key' => 'parameter', 'condition' => array ('type = ?', self::TYPE_PRODUCT)),
    array ('articles')
  );

  static $belongs_to = array (
  );

  const STATUS_1 = 1;
  const STATUS_2 = 2;

  static $statusNames = array (
    self::STATUS_1 => '下架',
    self::STATUS_2 => '上架',
  );

  public function __construct ($attrs = array (), $guardAttrs = true, $instantiatingViafind = false, $newRecord = true) {
    parent::__construct ($attrs, $guardAttrs, $instantiatingViafind, $newRecord);
  }

  public function destroy () {
    if (!isset ($this->id))
      return false;
    
    return $this->delete ();
  }
}
