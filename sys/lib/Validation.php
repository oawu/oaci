<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class Validation {
  private $val = null;
  private $title = null;
  private $exist = false;

  public function __construct (&$val, $title, $exist = true) {
    $this->val = &$val;
    $this->title = $title;
    $this->exist = $exist;
  }

  // ===================================================
  // Byte
  private function _size ($min, $max = null) {
    $this->isUploadFile ();

    function_exists ('byte_format') || Load::sysFunc ('number.php');

    $s = (int)$this->val['size'];
    $s >= $min || Validation::error ($this->title . '檔案要大於等於 ' . byte_format ($min));
    $max === null || $s <= $max || Validation::error ($this->title . '檔案要小於等於 ' . byte_format ($max));
  }

  private function _formats () {
    $extensions = array_unique (array_map ('trim', func_get_args ()));

    $this->isUploadFile ();

    if (!$extension = pathinfo ($this->val['name'], PATHINFO_EXTENSION)) {
      function_exists ('get_extension_by_mime') || Load::sysFunc ('file.php');
      $extension = get_extension_by_mime ($this->val['type']);
    }
    
    $extension || Validation::error ($this->title . '格式錯誤或不明');
    in_array ($extension, $extensions) || Validation::error ($this->title . '格式不符合');
  }

  // ===================================================

  private function _doArrayMap ($callback = null) {
    $this->isArray ();
    is_callable ($callback) && $this->val = array_map ($callback, $this->val);
  }

  private function _doArrayValues () {
    $this->isArray ();
    $this->val = array_values ($this->val);
  }

  private function _doArrayFilter ($callback = null) {
    $this->isArray ();
    $this->val = is_callable ($callback) ? array_filter ($this->val, $callback) : array_filter ($this->val);
  }

  private function _inArray (array $array) {
    $this->isStringOrNumber ();
    in_array ($this->val, $array) || Validation::error ($this->title . '需在指定的項目內');
  }

  private function _count ($min, $max = null) {
    $c = count ($this->val);
    $c >= $min || Validation::error ($this->title . '數量需要大於等於 ' . $min);
    $max === null || $c <= $max || Validation::error ($this->title . '數量需要小於等於 ' . $max);
  }

  // ===================================================
  // ==
  private function _equal ($num) {
    $this->isNumber ();
    $this->val == $num || Validation::error ($this->title . '需等於 ' . $num);
  }
  
  // ===
  private function _identical ($num) {
    $this->isNumber ();
    $this->val === $num || Validation::error ($this->title . '需完全等於 ' . $num);
  }
  
  // >
  private function _greater ($num) {
    $this->isNumber ();
    $this->val > $num || Validation::error ($this->title . '需大於 ' . $num);
  }
  
  // <
  private function _less ($num) {
    $this->isNumber ();
    $this->val < $num || Validation::error ($this->title . '需小於 ' . $num);
  }
  
  // >=
  private function _greaterEqual ($num) {
    $this->isNumber ();
    $this->val < $num || Validation::error ($this->title . '需大於以及等於 ' . $num);
  }
  
  // <=
  private function _lessEqual ($num) {
    $this->isNumber ();
    $this->val < $num || Validation::error ($this->title . '需小於以及等於 ' . $num);
  }
  
  // !=
  private function _notEqual ($num) {
    $this->isNumber ();
    $this->val < $num || Validation::error ($this->title . '需不等於 ' . $num);
  }
  
  // !==
  private function _notIdentical ($num) {
    $this->isNumber ();
    $this->val < $num || Validation::error ($this->title . '需完全不等於 ' . $num);
  }

  // ===================================================

  private function _doTrim ($character_mask = " \t\n\r\0\x0B") {
    $this->isStringOrNumber ();
    $this->val = trim ($this->val, $character_mask);
  }

  private function _mbLength ($min, $max = null) {
    $this->isStringOrNumber ();

    $l = mb_strlen ($this->val);
    $l >= $min || Validation::error ($this->title . '長度需要大於等於 ' . $min);
    $max === null || $l <= $max || Validation::error ($this->title . '長度需要小於等於 ' . $max);
  }

  private function _length ($min, $max = null) {
    $this->isStringOrNumber ();
    
    $l = strlen ($this->val);
    $l >= $min || Validation::error ($this->title . '長度需要大於等於 ' . $min);
    $max === null || $l <= $max || Validation::error ($this->title . '長度需要小於等於 ' . $max);
  }

  // ===================================================

  private function _isString ($msg = null) {
    is_string ($this->val) || Validation::error ($this->title . ($msg ? $msg : '格式必須是字串'));
  }

  private function _isNumber ($msg = null) {
    is_numeric ($this->val) || Validation::error ($this->title . ($msg ? $msg : '格式必須是數字'));
  }

  private function _isStringOrNumber ($msg = '') {
    is_string ($this->val) || is_numeric ($this->val) || Validation::error ($this->title . ($msg ? $msg : '需要是字串或數字'));
  }

  private function _isUploadFile ($msg = null) {
    is_array ($this->val) && count ($this->val) == 5 && isset ($this->val['name'], $this->val['type'], $this->val['tmp_name'], $this->val['error'], $this->val['size']) || Validation::error ($this->title . ($msg ? $msg : '格式必須是上傳檔案'));
  }

  private function _isArray ($msg = null) {
    is_array ($this->val) || Validation::error ($this->title . ($msg ? $msg : '格式必須是陣列'));
  }

  private function _isNull ($msg = null) {
    $this->val === null || Validation::error ($this->title . ($msg ? $msg : '格式必須是 NULL'));
  }

  private function _isNotNull ($msg = null) {
    $this->val !== null || Validation::error ($this->title . ($msg ? $msg : '格式必須是非 NULL'));
  }

  // ===================================================
  public function __call ($name, $args) {
    method_exists ($this, '_' . $name) || Validation::error ('Validation 錯誤的使用');
    $this->exist && call_user_func_array (array ($this, '_' . $name), $args);

    return $this;
  }
  // ===================================================

  public static function create (&$val, $title = '', $exist = true) {
    return new Validation ($val, $title, $exist);
  }

  public static function form ($closure, &...$args) {
    if (!is_callable ($closure))
      return '';
    
    try {
      call_user_func_array ($closure, $args);
      return '';
    } catch (Exception $e) {
      return $e->getMessage ();
    }
  }

  public static function error ($msg) {
    throw new Exception ($msg);
  }

  public static function need (&$arr, $key, $title = '') {
    isset ($arr[$key]) || self::error ($title . '錯誤！');
    return self::create ($arr[$key], $title);
  }
  public static function maybe (&$arr, $key, $title = '', $d4 = null) {
    if (isset ($arr[$key])) {
      return self::create ($arr[$key], $title, true);
    } else {
      $d4 === null || $arr[$key] = $d4;
      return self::create ($oa, $title, false);
    }
  }
}
