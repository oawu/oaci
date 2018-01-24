<?php

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

namespace AdminLib\Form;

defined ('OACI') || exit ('此檔案不允許讀取。');

include 'Form' . DIRECTORY_SEPARATOR . 'Row.php';
include 'Form' . DIRECTORY_SEPARATOR . 'Multi.php';

class Form {
  private $rows = array (), $obj = null, $hasImage = false;

  public function __construct ($obj = null) {
    $this->setObj ($obj);
  }

  public function setObj ($obj) {
    $this->obj = $obj;
    return $this;
  }
  public function appendRows () {
    $rows = func_get_args ();
    foreach ($rows as $row)
      $this->appendRow ($row);
    return $this;
  }
  public function appendRow (Row\Row $row) {
    if (!$this->hasImage && ($row instanceof Images || $row instanceof Image))
      $this->hasImage = true;

    array_push ($this->rows, $row);
    return $this;
  }

  public static function create () {
    $instance =  new Form ();
    foreach (func_get_args () as $row)
      $instance->appendRow ($row);
    return $instance;
  }

  public function __toString () {
    return $this->toString ();
  }

  public function toString () {
    $return = '';

    $return .= '<form class="form" action="' . ($this->obj ? \RestfulUrl::update ($this->obj) : \RestfulUrl::create ()) . '" method="post"' . ($this->hasImage ? ' enctype="multipart/form-data"' : '') . '>';
    $this->obj && $return .= '<input type="hidden" name="_method" value="put" />';

      foreach ($this->rows as $row)
        $return .= $row;
      $return .= '<div class="ctrl">';
        $return .= '<button type="submit">確定</button>';
        $return .= '<button type="reset">取消</button>';
      $return .= '</div>';
    $return .= '</form>';
    
    return $return;
  }
}

