<?php

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

namespace AdminLib\Form;

defined ('OACI') || exit ('此檔案不允許讀取。');


abstract class Row {
  protected $title = '', $name = '', $tip = '', $value = null, $need = false, $autofocus;

  public function __construct ($title = '') {
    $this->setTitle ($title);
  }

  public function isAutofocus ($autofocus) {
    $this->autofocus = $autofocus;
    return $this;
  }

  public function isNeed ($need) {
    $this->need = $need;
    return $this;
  }

  public function setValue ($value) {
    $this->value = $value;
    return $this;
  }

  public function setTip ($tip) {
    $this->tip = $tip;
    return $this;
  }

  public function setName ($name) {
    $this->name = $name;
    return $this;
  }

  public function setTitle ($title) {
    $this->title = $title;
    return $this;
  }

  public static function create ($title = '') {
    return new static ($title);
  }

  public function __toString () {
    return $this->toString ();
  }
}

abstract class RowInput extends Row {
  protected $type = '', $placeholder = '', $min = 0, $max = null;

  public function __construct ($title = '') {
    parent::__construct ($title);
    $this->setPlaceholder ('請填寫「' . $this->title . '」');
  }

  public function setPlaceholder ($placeholder) {
    $this->placeholder = $placeholder;
    return $this;
  }

  protected function setType ($type) {
    $this->type = $type;
    return $this;
  }
  
  public function setLength ($min, $max = null) {
    $min > 0 && $this->isNeed (true);

    $min < 0 || $this->min = $min;
    $max === null || $max < $min || $this->max = $max;
    return $this;
  }

  public function toString () {
    $return = '';

    if (!($this->title && $this->type && $this->name && $this->value !== null))
      return $return;

    $return .= '<label class="row">';
      $return .= '<b' . ($this->need ? ' class="need"' : '') .'' . ($this->tip ? ' data-tip="' . $this->tip . '"' : '') . '>' . $this->title . '</b>';
      $return .= '<input type="' . $this->type . '" name="' . $this->name . '"' . ($this->placeholder ? ' placeholder="' . $this->placeholder . '"' : '') . ' value="' . get_flash_params ($this->name, $this->value) . '"' . ($this->autofocus ? ' autofocus' : '') . ' minlength="' . $this->min . '"' . ($this->max !== null ? ' maxlength="' . $this->max . '"' : '') . '' . ($this->min > 0 ? ' required' : '') .' />';
    $return .= '</label>';

    return $return;
  }
}
  class RowText extends RowInput {
    public function __construct ($title = '') {
      parent::__construct ($title);
      $this->setType ('text');
    }
  }
  class RowColor extends RowInput {
    public function __construct ($title = '') {
      parent::__construct ($title);
      $this->setType ('color');
    }
  }
  class RowNumber extends RowInput {
    public function __construct ($title = '') {
      parent::__construct ($title);
      $this->setType ('number');
    }
  }
  class RowDate extends RowInput {
    public function __construct ($title = '') {
      parent::__construct ($title);
      $this->setType ('date');
    }
  }

abstract class RowInputTextArea extends RowInput {
  public function __construct ($title = '') {
    parent::__construct ($title);
  }

  public function toString () {
    $return = '';
    
    if (!($this->title && $this->type && $this->name && $this->value !== null))
      return $return;

    $return .= '<div class="row">';
    $return .= '<b' . ($this->need ? ' class="need"' : '') .'' . ($this->tip ? ' data-tip="' . $this->tip . '"' : '') . '>' . $this->title . '</b>';
    $return .= '<textarea class="' . $this->type . '" name="' . $this->name . '">' . get_flash_params ($this->name, $this->value) . '</textarea>';
    $return .= '</div>';

    return $return;
  }
}
  class RowTextArea extends RowInputTextArea {
    public function __construct ($title = '') {
      parent::__construct ($title);
      $this->setType ('pure');
    }
  }
  class RowCkeditor extends RowInputTextArea {
    public function __construct ($title = '') {
      parent::__construct ($title);
      $this->setType ('ckeditor');
    }
  }


class RowImages extends Row {
  protected $srcs = array ();
  private $accept = 'image/*';

  public function __construct ($title = '') {
    parent::__construct ($title);
    $this->appendSrc ('');
  }

  public function appendSrcs ($srcs) {
    foreach ($srcs as $src)
      $this->appendSrc ($src);
    return $this;
  }

  public function appendSrc ($src) {
    is_string ($src) && array_push ($this->srcs, $src);
    return $this;
  }

  public function toString () {
    $return = '';

    if (!($this->title && $this->name))
      return $return;

    $accept = $this->accept;
    $name = $this->name . '[]';

    $return .= '<div class="row">';
      $return .= '<b' . ($this->need ? ' class="need"' : '') .'' . ($this->tip ? ' data-tip="' . $this->tip . '"' : '') . '>' . $this->title . '</b>';
      $return .= '<div class="multi-drop-imgs">';
      $return .= implode ('', array_map (function ($src) use ($name, $accept) {
        $return = '<div class="drop-img">';
          $return .= '<img src="' . $src . '" />';
          $return .= '<input type="file" name="' . $name . '" accept="' . $accept . '" />';
          $return .= '<a class="icon-04"></a>';
        $return .= '</div>';
        return $return;
      }, array_unique ($this->srcs)));
      $return .= '</div>';
    $return .= '</div>';

    return $return;
  }
}
class RowImage extends Row {
  private $accept = 'image/*';

  public function setAccept ($accept) {
    $this->accept = $accept;
    return $this;
  }

  public function toString () {
    $return = '';

    if (!($this->title && $this->name && $this->value !== null))
      return $return;

    $return .= '<div class="row">';
      $return .= '<b' . ($this->need ? ' class="need"' : '') .'' . ($this->tip ? ' data-tip="' . $this->tip . '"' : '') . '>' . $this->title . '</b>';
      $return .= '<div class="drop-img">';
        $return .= '<img src="' . ($this->value ? '' . $this->value . '' : '') . '" />';
        $return .= '<input type="file" name="' . $this->name . '" accept="' . $this->accept . '" />';
      $return .= '</div>';
    $return .= '</div>';

    return $return;
  }
}

class RowItem {
  private $text, $value;

  public function __construct ($value, $text = '') {
    $this->setValue ($value)
         ->setText ($text);
  }
  
  public function setText ($text) {
    is_string ($text) && $this->text = $text;
    return $this;
  }
  
  public function setValue ($value) {
    is_string ($value) && $this->value = $value;
    return $this;
  }

  public function getText () {
    return $this->text;
  }
  
  public function getValue () {
    return $this->value;
  }
  
  public static function create ($value, $text = '') {
    return new RowItem ($value, $text);
  }

  public static function combine ($values, $texts) {
    if (count ($values) != count ($texts))
      return array ();

    return array_map (function ($value, $text) { return RowItem::create ($value, $text); }, $values, $texts);
  }
}

abstract class RowItemChoice extends Row {
  protected $items = array ();

  public function appendItems ($items) {
    foreach ($items as $item)
      $this->appendItem ($item);
    return $this;
  }

  public function appendItem (RowItem $item) {
    array_push ($this->items, $item);
    return $this;
  }
}

  class RowSelect extends RowItemChoice {
    public function toString () {
      $return = '';

      if (!($this->title && $this->name && $this->value !== null))
        return $return;

      $return .= '<div class="row">';
        $return .= '<b' . ($this->need ? ' class="need"' : '') .'' . ($this->tip ? ' data-tip="' . $this->tip . '"' : '') . '>' . $this->title . '</b>';
        $return .= '<select name="' . $this->name . '">';
          $return .= '<option value=""' . (get_flash_params ($this->name, $this->value, '') ? ' selected' : '') . '>請選擇' . $this->title . '</option>';
        foreach ($this->items as $item)
          $return .= '<option value="' . $item->getValue () . '"' . (get_flash_params ($this->name, $this->value, $item->getValue ()) ? ' selected' : '') . '>' . $item->getText () . '</option>';
        $return .= '</select>';
      $return .= '</div>';

      return $return;
    }
  }

  class RowRadios extends RowItemChoice {
    public function toString () {
      $return = '';

      if (!($this->title && $this->name && $this->value !== null))
        return $return;

      $return .= '<div class="row">';
        $return .= '<b' . ($this->need ? ' class="need"' : '') .'' . ($this->tip ? ' data-tip="' . $this->tip . '"' : '') . '>' . $this->title . '</b>';
        $return .= '<div class="radios">';
        
        foreach ($this->items as $item)
          $return .= form_radio ($this->name, $item->getValue (), $item->getText (), get_flash_params ($this->name, $this->value, $item->getValue ()));
        $return .= '</div>';
      $return .= '</div>';

      return $return;
    }
  }

  class RowCheckboxs extends RowItemChoice {
    public function toString () {
      $return = '';

      if (!($this->title && $this->name && $this->value !== null))
        return $return;

      $return .= '<div class="row">';
        $return .= '<b' . ($this->need ? ' class="need"' : '') .'' . ($this->tip ? ' data-tip="' . $this->tip . '"' : '') . '>' . $this->title . '</b>';
        $return .= '<div class="checkboxs">';

        foreach ($this->items as $item)
          $return .= form_checkbox ($this->name . '[]', $item->getValue (), $item->getText (), get_flash_params ($this->name, $this->value, $item->getValue ()));
        
        $return .= '</div>';
      $return .= '</div>';

      return $return;
    }
  }

  class RowSwitch extends RowItemChoice {
    public function toString () {
      $return = '';

      if (!($this->title && $this->name && $this->value !== null))
        return $return;

      $return .= '<div class="row min">';
        $return .= '<b' . ($this->need ? ' class="need"' : '') .'' . ($this->tip ? ' data-tip="' . $this->tip . '"' : '') . '>' . $this->title . '</b>' . ' ';
        $return .= '<div class="switches">';

        foreach ($this->items as $item)
          $return .= form_switch ($this->name, $item->getValue (), '', get_flash_params ($this->name, $this->value, $item->getValue ()));
        
        $return .= '</div>';
      $return .= '</div>';

      return $return;
    }
  }

class RowLatLng extends Row {
  private $latName = 'latitude';
  private $lngName = 'longitude';
  private $latValue = 23.79539759;
  private $lngValue = 120.88256835;
  private $step = 'any';

  public function setLat ($name, $value = '') {
    $this->latName = $name;
    $value > -85 && $value < 85 && $this->latValue = $value;
    return $this;
  }
  public function setLng ($name, $value = '') {
    $this->lngName = $name;
    $value > -180 && $value < 180 && $this->lngValue = $value;
    return $this;
  }
  public function setStep ($step) {
    $step && is_numeric ($step) && $this->step = $step;
    return $this;
  }
  public function toString () {
    $return = '';

    if (!($this->title && $this->latName && $this->lngName))
      return $return;

    $return .= '<div class="row">';
      $return .= '<b' . ($this->need ? ' class="need"' : '') .'' . ($this->tip ? ' data-tip="' . $this->tip . '"' : '') . '>' . $this->title . '</b>';
      $return .= '<div class="map-edit">';
        $return .= '<input type="number" name="' . $this->latName . '" step="' . $this->step . '" value="' . get_flash_params ($this->latName, $this->latValue) . '" />';
        $return .= '<input type="number" name="' . $this->lngName . '" step="' . $this->step . '" value="' . get_flash_params ($this->lngName, $this->lngValue) . '" />';
      $return .= '</div>';
    $return .= '</div>';

    return $return;
  }
}

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
  public function appendRow (Row $row) {
    if (!$this->hasImage && ($row instanceof RowImages || $row instanceof RowImage))
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