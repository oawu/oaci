<?php

namespace Restful;

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

defined ('OACI') || exit ('此檔案不允許讀取。');

class Form {
  private $rows = array (), $hasImage = false;

  public static function create () {
    return new Form ();
  }

  public function appendFormRows () {
    foreach (func_get_args () as $row)
      $this->appendFormRow ($row);
    return $this;
  }

  public function appendFormRow (Row $row) {
    $this->hasImage |= $row instanceof Images || $row instanceof Image;
    array_push ($this->rows, $row);
    return $this;
  }
  public function setObj ($obj) {
    $this->obj = $obj;
    return $this;
  }

  public function __toString () {
    return $this->toString ();
  }

  public function toString () {
    $return = '';

    $return .= '<form class="form" action="' . ($this->obj ? \RestfulUrl::update ($this->obj) : \RestfulUrl::create ()) . '" method="post"' . ($this->hasImage ? ' enctype="multipart/form-data"' : '') . '>';
    $this->obj && $return .= '<input type="hidden" name="_method" value="put" />';

      foreach ($this->rows as $row)
        $return .= $row->setObj ($this->obj);

      $return .= '<div class="ctrl">';
        $return .= '<button type="submit">確定</button>';
        $return .= '<button type="reset">取消</button>';
      $return .= '</div>';
    $return .= '</form>';
    
    return $return;
  }
}

abstract class Row {
  protected $title, $tip, $need, $obj;

  public function __toString () {
    return $this->toString ();
  }

  public function setObj ($obj) { $this->obj = $obj; return $this; }
  public function setTitle ($title) { $title && is_string ($title) && $this->title = $title; return $this; }
  public function setTip ($tip) { $tip && is_string ($tip) && $this->tip = $tip; return $this; }
  public function setNeed ($need) { is_bool ($need) && $this->need = $need; return $this; }

  protected static function attrs ($arr) {
    return $arr ? ' ' . implode (' ', $arr) : '';
  }
  protected function b () {
    $attrs = array ();
    $this->need && array_push ($attrs, 'class="need"');
    $this->tip  && array_push ($attrs, 'data-tip="' . $this->tip . '"');
    return '<b' . self::attrs ($attrs) . '>' . $this->title . '</b>';
  }
}

abstract class Input extends Row {
  protected $name, $value, $autofocus, $placeholder, $type, $min, $max;

  public function __construct () {
    $this->title = null;
    $this->name = null;

    $this->value = '';
    $this->need = false;
    $this->autofocus = false;
    $this->placeholder = '';
    $this->tip = '';
    $this->type = null;
    $this->min = 0;
    $this->max = null;
  }

  public static function create ($title, $name, $value = '') { return (new static ())->setTitle ($title)->setName ($name)->setValue ($value); }
  public static function need ($title, $name, $value = '') { return self::create ($title, $name, $value)->setNeed (true); }
  public static function maybe ($title, $name, $value = '') { return self::create ($title, $name, $value)->setNeed (false); }

  protected function setType ($type) { $type && is_string ($type) && in_array ($type, array ('text', 'color', 'number', 'date', 'email')) && $this->type = $type; return $this; }

  public function setName ($name) { $name && is_string ($name) && $this->name = $name; return $this; }
  public function setValue ($value) { is_string ($value) && $this->value = $value; return $this; }

  public function setAutofocus ($autofocus) { is_bool ($autofocus) && $this->autofocus = $autofocus; return $this; }
  public function setPlaceholder ($placeholder) { is_string ($placeholder) && $this->placeholder = $placeholder; return $this; }

  public function setMinLength ($min) { is_numeric ($min) && ($this->need ? $min > 0 : $min >= 0) && $this->min = $min; return $this; }
  public function setMaxLength ($max) { is_numeric ($max) && $max >= $this->min && $this->max = $max; return $this; }
  public function setLength ($min, $max) { return $this->setMinLength ($min)->setMaxLength ($max); }


  public function toString () {
    $return = '';

    if ($this->title === null || $this->name === null || $this->type === null)
      return $return;
    
    $this->placeholder || $this->setPlaceholder ('請填寫「' . $this->title . '」');

    $v = get_flash_params ($this->name, $this->value);
    $this->obj && isset ($this->obj->{$this->name}) && $v = $this->obj->{$this->name};
    $v instanceof \Uploader && $v = $v->url ();
    $v instanceof ActiveRecord\DateTime && ($v = $v->format ($this instanceof \Date ? 'Y-m-d' : 'Y-m-d H:i:s'));
    is_object ($v) && $v = (string)$v;

    $attrs = array ('type="' . $this->type . '"', 'name="' . $this->name . '"', 'value="' . $v . '"');
    $this->need        && array_push ($attrs, 'minlength="' . $this->min . '"');
    $this->max         && array_push ($attrs, 'maxlength="' . $this->max . '"');
    $this->placeholder && array_push ($attrs, 'placeholder="' . $this->placeholder . '"');
    $this->autofocus   && array_push ($attrs, 'autofocus');
    $this->need        && array_push ($attrs, 'required');

    $return .= '<label class="row">';
      $return .= $this->b ();
      $return .= '<input' . self::attrs ($attrs) .'/>';
    $return .= '</label>';

    return $return;
  }
}

class Text extends Input {
  public function __construct () {
    parent::__construct ();
    $this->setType ('text');
  }
}
class Color extends Input {
  public function __construct () {
    parent::__construct ();
    $this->setType ('color');
  }
}
class Number extends Input {
  public function __construct () {
    parent::__construct ();
    $this->setType ('number');
  }
}
class Date extends Input {
  public function __construct () {
    parent::__construct ();
    $this->setType ('date');
  }
}
class Email extends Input {
  public function __construct () {
    parent::__construct ();
    $this->setType ('email');
  }
}










// abstract class TextArea extends Input {
//   public function __construct ($title = '') {
//     parent::__construct ($title);
//   }

//   public function toString () {
//     $return = '';
    
//     if (!($this->title && $this->type && $this->name && $this->value !== null))
//       return $return;

//     $return .= '<div class="row">';
//     $return .= '<b' . ($this->need ? ' class="need"' : '') .'' . ($this->tip ? ' data-tip="' . $this->tip . '"' : '') . '>' . $this->title . '</b>';
//     $return .= '<textarea class="' . $this->type . '" name="' . $this->name . '">' . get_flash_params ($this->name, $this->value) . '</textarea>';
//     $return .= '</div>';

//     return $return;
//   }
// }
// class PureText extends TextArea {
//   public function __construct ($title = '') {
//     parent::__construct ($title);
//     $this->setType ('pure');
//   }
// }
// class Ckeditor extends TextArea {
//   public function __construct ($title = '') {
//     parent::__construct ($title);
//     $this->setType ('ckeditor');
//   }
// }


// class Images extends General {
//   protected $srcs = array ();
//   private $accept = 'image/*';

//   public function __construct ($title = '') {
//     parent::__construct ($title);
//     $this->appendSrc ('');
//   }

//   public function appendSrcs ($srcs) {
//     foreach ($srcs as $src)
//       $this->appendSrc ($src);
//     return $this;
//   }

//   public function appendSrc ($src) {
//     is_string ($src) && array_push ($this->srcs, $src);
//     return $this;
//   }

//   public function toString () {
//     $return = '';

//     if (!($this->title && $this->name))
//       return $return;

//     $accept = $this->accept;
//     $name = $this->name . '[]';

//     $return .= '<div class="row">';
//       $return .= '<b' . ($this->need ? ' class="need"' : '') .'' . ($this->tip ? ' data-tip="' . $this->tip . '"' : '') . '>' . $this->title . '</b>';
//       $return .= '<div class="multi-drop-imgs">';
//       $return .= implode ('', array_map (function ($src) use ($name, $accept) {
//         $return = '<div class="drop-img">';
//           $return .= '<img src="' . $src . '" />';
//           $return .= '<input type="file" name="' . $name . '" accept="' . $accept . '" />';
//           $return .= '<a class="icon-04"></a>';
//         $return .= '</div>';
//         return $return;
//       }, array_unique ($this->srcs)));
//       $return .= '</div>';
//     $return .= '</div>';

//     return $return;
//   }
// }
// class Image extends General {
//   private $accept = 'image/*';

//   public function setAccept ($accept) {
//     $this->accept = $accept;
//     return $this;
//   }

//   public function toString () {
//     $return = '';

//     if (!($this->title && $this->name && $this->value !== null))
//       return $return;

//     $return .= '<div class="row">';
//       $return .= '<b' . ($this->need ? ' class="need"' : '') .'' . ($this->tip ? ' data-tip="' . $this->tip . '"' : '') . '>' . $this->title . '</b>';
//       $return .= '<div class="drop-img">';
//         $return .= '<img src="' . ($this->value ? '' . $this->value . '' : '') . '" />';
//         $return .= '<input type="file" name="' . $this->name . '" accept="' . $this->accept . '" />';
//       $return .= '</div>';
//     $return .= '</div>';

//     return $return;
//   }
// }

// abstract class ChoiceItem {
//   private $text, $value;

//   public function __construct ($value, $text = '') {
//     $this->setValue ($value)
//          ->setText ($text);
//   }
  
//   public function setText ($text) {
//     is_string ($text) && $this->text = $text;
//     return $this;
//   }
  
//   public function setValue ($value) {
//     $this->value = $value;
//     return $this;
//   }

//   public function getText () {
//     return $this->text;
//   }
  
//   public function getValue () {
//     return $this->value;
//   }
  
//   public static function create ($value, $text = '') {
//     return new static ($value, $text);
//   }

//   public static function combine ($values, $texts) {
//     if (count ($values) != count ($texts))
//       return array ();

//     return array_map (function ($value, $text) { return static::create ($value, $text); }, $values, $texts);
//   }
// }
// class Option extends ChoiceItem {}
// class Radio extends ChoiceItem {}
// class Checkbox extends ChoiceItem {}
// class Switcher extends ChoiceItem {}

// interface ChoicerInterface {
//   public function appendItem ($item);
// }

// abstract class Choicer extends General implements ChoicerInterface {
//   protected $items = array ();

//   public function appendCombine ($items) {
//     foreach ($items as $item)
//       $this->appendItem ($item);
//     return $this;
//   }
// }

// class Select extends Choicer {

//   public function appendItem ($item) {
//     $item instanceof Option && array_push ($this->items, $item);
//     return $this;
//   }

//   public function toString () {
//     $return = '';

//     if (!($this->title && $this->name && $this->value !== null))
//       return $return;

//     $return .= '<div class="row">';
//       $return .= '<b' . ($this->need ? ' class="need"' : '') .'' . ($this->tip ? ' data-tip="' . $this->tip . '"' : '') . '>' . $this->title . '</b>';
//       $return .= '<select name="' . $this->name . '"' . ($this->need ? ' required' : '') .'>';
//         $return .= '<option value=""' . (get_flash_params ($this->name, $this->value, '') ? ' selected' : '') . '>請選擇' . $this->title . '</option>';
//       foreach ($this->items as $item)
//         $return .= '<option value="' . $item->getValue () . '"' . (get_flash_params ($this->name, $this->value, $item->getValue ()) ? ' selected' : '') . '>' . $item->getText () . '</option>';
//       $return .= '</select>';
//     $return .= '</div>';

//     return $return;
//   }
// }

// class Radios extends Choicer {
  
//   public function appendItem ($item) {
//     $item instanceof Radio && array_push ($this->items, $item);
//     return $this;
//   }

//   public function toString () {
//     $return = '';

//     if (!($this->title && $this->name && $this->value !== null))
//       return $return;

//     $return .= '<div class="row">';
//       $return .= '<b' . ($this->need ? ' class="need"' : '') .'' . ($this->tip ? ' data-tip="' . $this->tip . '"' : '') . '>' . $this->title . '</b>';
//       $return .= '<div class="radios">';
//       // ' . $this->name . '"' . ($this->need ? ' required' : '') .
//       foreach ($this->items as $item)
//         $return .= form_radio ($this->name, $item->getValue (), $item->getText (), get_flash_params ($this->name, $this->value, $item->getValue ()), array (), $this->need ? array ('required' => null) : array ());
//       $return .= '</div>';
//     $return .= '</div>';

//     return $return;
//   }
// }

// class Checkboxs extends Choicer {
//   public function appendItem ($item) {
//     $item instanceof Checkbox && array_push ($this->items, $item);
//     return $this;
//   }
//   public function toString () {
//     $return = '';

//     if (!($this->title && $this->name && $this->value !== null))
//       return $return;

//     $return .= '<div class="row">';
//       $return .= '<b' . ($this->need ? ' class="need"' : '') .'' . ($this->tip ? ' data-tip="' . $this->tip . '"' : '') . '>' . $this->title . '</b>';
//       $return .= '<div class="checkboxs">';

//       foreach ($this->items as $item)
//         $return .= form_checkbox ($this->name . '[]', $item->getValue (), $item->getText (), get_flash_params ($this->name, $this->value, $item->getValue ()));
      
//       $return .= '</div>';
//     $return .= '</div>';

//     return $return;
//   }
// }

// class Switchers extends Choicer {
//   public function appendItem ($item) {
//     $item instanceof Switcher && array_push ($this->items, $item);
//     return $this;
//   }
//   public function toString () {
//     $return = '';

//     if (!($this->title && $this->name && $this->value !== null))
//       return $return;

//     $return .= '<div class="row min">';
//       $return .= '<b' . ($this->need ? ' class="need"' : '') .'' . ($this->tip ? ' data-tip="' . $this->tip . '"' : '') . '>' . $this->title . '</b>' . ' ';
//       $return .= '<div class="switches">';

//       foreach ($this->items as $item)
//         $return .= form_switch ($this->name, $item->getValue (), '', get_flash_params ($this->name, $this->value, $item->getValue ()));
      
//       $return .= '</div>';
//     $return .= '</div>';

//     return $return;
//   }
// }

// class LatLng extends Row {
//   private $latName = 'latitude';
//   private $lngName = 'longitude';
//   private $latValue = 23.79539759;
//   private $lngValue = 120.88256835;
//   private $step = 'any';

//   public function setLat ($name, $value = '') {
//     $this->latName = $name;
//     $value > -85 && $value < 85 && $this->latValue = $value;
//     return $this;
//   }
//   public function setLng ($name, $value = '') {
//     $this->lngName = $name;
//     $value > -180 && $value < 180 && $this->lngValue = $value;
//     return $this;
//   }
//   public function setStep ($step) {
//     $step && is_numeric ($step) && $this->step = $step;
//     return $this;
//   }
//   public function toString () {
//     $return = '';

//     if (!($this->title && $this->latName && $this->lngName))
//       return $return;

//     $return .= '<div class="row">';
//       $return .= '<b' . ($this->need ? ' class="need"' : '') .'' . ($this->tip ? ' data-tip="' . $this->tip . '"' : '') . '>' . $this->title . '</b>';
//       $return .= '<div class="map-edit">';
//         $return .= '<input type="number" name="' . $this->latName . '" step="' . $this->step . '" value="' . get_flash_params ($this->latName, $this->latValue) . '" />';
//         $return .= '<input type="number" name="' . $this->lngName . '" step="' . $this->step . '" value="' . get_flash_params ($this->lngName, $this->lngValue) . '" />';
//       $return .= '</div>';
//     $return .= '</div>';

//     return $return;
//   }
// }

// class Multi extends Row {
//   protected $name = '', $value = array (), $columns = array ();

//   // public function __construct ($title = '') {
//   //   parent::__construct ($title);
//   // }

//   public function setColumns () {
//     $this->columns = array_filter (func_get_args (), function ($t) { return in_array (get_class ($t), array (
//       'AdminLib\Form\Row\Multi\Text',
//       'AdminLib\Form\Row\Multi\Select',
//       'AdminLib\Form\Row\Multi\Checkboxs')); });
//     return $this;
//   }

//   public function setName ($name) {
//     $this->name = $name;
//     return $this;
//   }

//   public function setValue ($value) {
//     is_array ($this->value) && $this->value = $value;
//     return $this;
//   }
//   public function toString () {
//     $return = '';

//     $names = array_map (function ($t) { return $t->getName(); }, $this->columns);

//     $flash = get_flash_params ('sources', false);
//     $datas = $flash !== false ? $flash : $this->value;
//     $datas = array_values (array_filter (array_map (function ($data) use ($names) { $nData = array (); foreach ($names as $name) if (isset ($data[$name])) $nData[$name] = $data[$name]; else return null; return $nData; }, $datas)));
//     array_unshift ($datas, '');


//     if (!($this->title))
//       return $return;

//     $return .= '<div class="row">';
//       $return .= '<b' . ($this->need ? ' class="need"' : '') .'' . ($this->tip ? ' data-tip="' . $this->tip . '"' : '') . '>' . $this->title . '</b>';
//       $return .= '<div class="multi-datas" data-index="' . (count ($datas) - 1) . '">';

//         foreach ($datas as $i => $data) {
//           $return .= '<div class="datas' . ($data === '' ? ' demo' : '') . '">';

//           foreach ($this->columns as $column)
//             $return .= $data === '' ? $column->isDemo (true)->setValue ('')->setPrefix ($this->name) : $column->isDemo (false)->setValue ($data[$column->getName ()])->setPrefix ($this->name . '[' . ($i - 1) . ']');

//             $return .= '<a class="icon-04 del"></a>';
//           $return .= '</div>';
//         }

//         $return .= '<div class="btns">';
//           $return .= '<a class="icon-07 add">參考鏈結</a>';
//         $return .= '</div>';
//       $return .= '</div>';
//     $return .= '</div>';

//     return $return;
//   }
// }
