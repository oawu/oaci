<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class Validation {
  private $error = null;
  private $val = null;
  private $title = null;

  public function __construct (&$val, $title) {
    $this->val = &$val;
    $this->error = null;
    $this->title = $title;
  }

  public function getError () {
    return $this->error;
  }

  public function doArrayMap ($callback = null) {
    $this->isArray ();

    if ($this->error)
      return $this;
    
    is_callable ($callback) && $this->val = array_map ($callback, $this->val);
    return $this;
  }

  public function doArrayValues () {
    $this->isArray ();

    if ($this->error)
      return $this;

    $this->val = array_values ($this->val);
    return $this;
  }

  public function doArrayFilter ($callback = null) {
    $this->isArray ();

    if ($this->error)
      return $this;

    $this->val = is_callable ($callback) ? array_filter ($this->val, $callback) : array_filter ($this->val);
    return $this;
  }

  public function doTrim ($character_mask = " \t\n\r\0\x0B") {
    $this->isStringOrNumber ();

    if ($this->error)
      return $this;

    $this->val = trim ($this->val, $character_mask);
    return $this;
  }
  // ==
  public function equal ($num) {
    $this->isNumber ();

    if ($this->error)
      return $this;
    
    $this->val == $num || $this->error = $this->title . '需等於 ' . $num;
    return $this;
  }
  // ===
  public function identical ($num) {
    $this->isNumber ();

    if ($this->error)
      return $this;
    
    $this->val === $num || $this->error = $this->title . '需完全等於 ' . $num;
    return $this;
  }
  // >
  public function greater ($num) {
    $this->isNumber ();

    if ($this->error)
      return $this;
    
    $this->val > $num || $this->error = $this->title . '需大於 ' . $num;
    return $this;
  }
  // <
  public function less ($num) {
    $this->isNumber ();

    if ($this->error)
      return $this;
    
    $this->val < $num || $this->error = $this->title . '需小於 ' . $num;
    return $this;
  }
  // >=
  public function greaterEqual ($num) {
    $this->isNumber ();

    if ($this->error)
      return $this;
    
    $this->val < $num || $this->error = $this->title . '需大於以及等於 ' . $num;
    return $this;
  }
  // <=
  public function lessEqual ($num) {
    $this->isNumber ();

    if ($this->error)
      return $this;
    
    $this->val < $num || $this->error = $this->title . '需小於以及等於 ' . $num;
    return $this;
  }
  // !=
  public function notEqual ($num) {
    $this->isNumber ();

    if ($this->error)
      return $this;
    
    $this->val < $num || $this->error = $this->title . '需不等於 ' . $num;
    return $this;
  }
  // !==
  public function notIdentical ($num) {
    $this->isNumber ();

    if ($this->error)
      return $this;
    
    $this->val < $num || $this->error = $this->title . '需完全不等於 ' . $num;
    return $this;
  }

  public function inArray (array $array) {
    $this->isStringOrNumber ();

    if ($this->error)
      return $this;
    
    in_array ($this->val, $array) || $this->error = $this->title . '需在指定的項目內';
    return $this;
  }

  public function count ($min, $max = null) {
    if ($this->error)
      return $this;

    $c = count ($this->val);
    $c >= $min || $this->error = $this->title . '數量需要大於等於 ' . $min;
    $max === null || $c <= $max || $this->error = $this->title . '數量需要小於等於 ' . $max;
    return $this;
  }

  public function mbLength ($min, $max = null) {
    $this->isStringOrNumber ();
    
    if ($this->error)
      return $this;

    $l = mb_strlen ($this->val);
    $l >= $min || $this->error = $this->title . '長度需要大於等於 ' . $min;
    $max === null || $l <= $max || $this->error = $this->title . '長度需要小於等於 ' . $max;
    return $this;
  }

  public function length ($min, $max = null) {
    $this->isStringOrNumber ();
    
    if ($this->error)
      return $this;

    $l = strlen ($this->val);
    $l >= $min || $this->error = $this->title . '長度需要大於等於 ' . $min;
    $max === null || $l <= $max || $this->error = $this->title . '長度需要小於等於 ' . $max;
    return $this;
  }
  // Byte
  public function size ($min, $max = null) {
    $this->isUploadFile ();

    if ($this->error)
      return $this;
    
    function_exists ('byte_format') || Load::sysFunc ('number.php');

    $s = (int)$this->val['size'];
    $s >= $min || $this->error = $this->title . '檔案要大於等於 ' . byte_format ($min);
    $max === null || $s <= $max || $this->error = $this->title . '檔案要小於等於 ' . byte_format ($max);
    return $this;
  }

  public function formats () {
    $extensions = array_unique (array_map ('trim', func_get_args ()));

    $this->isUploadFile ();

    if ($this->error)
      return $this;


    if (!$extension = pathinfo ($this->val['name'], PATHINFO_EXTENSION)) {
      function_exists ('get_extension_by_mime') || Load::sysFunc ('file.php');
      $extension = get_extension_by_mime ($this->val['type']);
    }
    
    $extension || $this->error = $this->title . '格式錯誤或不明';
    in_array ($extension, $extensions) || $this->error = $this->title . '格式不符合';

    return $this;
  }




  public function isUploadFile ($error = null) {
    if ($this->error)
      return $this;
    
    is_array ($this->val) && count ($this->val) == 5 && isset ($this->val['name'], $this->val['type'], $this->val['tmp_name'], $this->val['error'], $this->val['size']) || $this->error = $this->title . ($error ? $error : '格式必須是上傳檔案');

    return $this;
  }

  public function isString ($error = null) {
    if ($this->error)
      return $this;

    is_string ($this->val) || $this->error = $this->title . ($error ? $error : '格式必須是字串');
    return $this;
  }

  public function isNumber ($error = null) {
    if ($this->error)
      return $this;

    is_numeric ($this->val) || $this->error = $this->title . ($error ? $error : '格式必須是數字');
    return $this;
  }

  public function isStringOrNumber ($error = null) {
    if ($this->error)
      return $this;

    is_string ($this->val) || is_numeric ($this->val) || $this->error = $this->title . ($error ? $error : '需要是字串或數字');
    return $this;
  }

  public function isArray ($error = null) {
    if ($this->error)
      return $this;

    is_array ($this->val) || $this->error = $this->title . ($error ? $error : '格式必須是陣列');
    return $this;
  }

  public function isNull ($error = null) {
    if ($this->error)
      return $this;

    $this->val === null || $this->error = $this->title . ($error ? $error : '格式必須是 NULL');
    return $this;
  }

  public function isNotNull ($error = null) {
    if ($this->error)
      return $this;

    $this->val !== null || $this->error = $this->title . ($error ? $error : '格式必須是非 NULL');
    return $this;
  }


  

  public static function create (&$val, $title = '') {
    return new Validation ($val, $title);
  }

  // public static function exist (&$val, $title = '') {
  //   $v = self::create ($val, $title)
  //       ->isNull ()
  //       ->getError ();
  //       echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
  //       var_dump ($v);
  //       exit ();
  //   // if ($val === null)

  //   return new Validation ($val, $title);
  // }
}
