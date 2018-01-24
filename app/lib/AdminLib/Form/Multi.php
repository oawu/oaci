<?php

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

namespace AdminLib\Form\Row\Multi;
use AdminLib\Form\Row as Row;

defined ('OACI') || exit ('此檔案不允許讀取。');

class Text extends Row\Input {
  private $prefix = '', $demo = false;

  public function isDemo ($demo) {
    $this->demo = $demo;
    return $this;
  }
  public function setPrefix ($prefix) {
    $this->prefix = $prefix;
    return $this;
  }

  public function getName () {
    return $this->name;
  }

  public function toString () {
    $return = '';

    if (!($this->title && $this->name && $this->value !== null))
      return $return;

    $return .= '<div' . ($this->need ? ' class="need"' : '') .'>';

    if ($this->demo)
      $return .= '<input type="text" data-prefix="' . $this->prefix . '" data-name="[' . $this->name . ']"' . ($this->placeholder ? ' placeholder="' . $this->placeholder . '"' : '') . ' value="' . $this->value . '" minlength="' . $this->min . '"' . ($this->max !== null ? ' maxlength="' . $this->max . '"' : '') . '' . ($this->min > 0 ? ' required' : '') .' />';
    else
      $return .= '<input type="text" name="' . $this->prefix . '[' . $this->name . ']' . '"' . ($this->placeholder ? ' placeholder="' . $this->placeholder . '"' : '') . ' value="' . $this->value . '" minlength="' . $this->min . '"' . ($this->max !== null ? ' maxlength="' . $this->max . '"' : '') . '' . ($this->min > 0 ? ' required' : '') .' />';

    $return .= '</div>';

    return $return;
  }
}

class Select extends Row\Select {
  private $prefix = '', $demo = false;

  public function isDemo ($demo) {
    $this->demo = $demo;
    return $this;
  }
  public function setPrefix ($prefix) {
    $this->prefix = $prefix;
    return $this;
  }

  public function getName () {
    return $this->name;
  }

  public function toString () {
    $return = '';

    if (!($this->title && $this->name && $this->value !== null))
      return $return;

    $return .= '<div' . ($this->need ? ' class="need"' : '') .'>';
      if ($this->demo) {
        $return .= '<select data-prefix="' . $this->prefix . '" data-name="[' . $this->name . ']">';
          $return .= '<option value=""' . ($this->value === '' ? ' selected' : '') . '>請選擇' . $this->title . '</option>';
          foreach ($this->items as $item)
            $return .= '<option value="' . $item->getValue () . '"' . ($this->value == $item->getValue () ? ' selected' : '') . '>' . $item->getText () . '</option>';
        $return .= '</select>';
      } else {
        $return .= '<select name="' . $this->prefix . '[' . $this->name . ']' . '">';
          $return .= '<option value=""' . ($this->value === '' ? ' selected' : '') . '>請選擇' . $this->title . '</option>';
          foreach ($this->items as $item)
            $return .= '<option value="' . $item->getValue () . '"' . ($this->value == $item->getValue () ? ' selected' : '') . '>' . $item->getText () . '</option>';
        $return .= '</select>';
      }
    $return .= '</div>';

    return $return;
  }
}

class Checkboxs extends Row\Checkboxs {
  private $prefix = '', $demo = false;

  public function isDemo ($demo) {
    $this->demo = $demo;
    return $this;
  }
  public function setPrefix ($prefix) {
    $this->prefix = $prefix;
    return $this;
  }

  public function getName () {
    return $this->name;
  }

  public function toString () {
    $return = '';

    if (!($this->title && $this->name && $this->value !== null))
      return $return;

    $return .= '<div' . ($this->need ? ' class="need"' : '') .'>';
      if ($this->demo)
        foreach ($this->items as $item)
          $return .= form_checkbox ('', $item->getValue (), $item->getText (), is_array ($this->value) ? in_array ($item->getValue (), $this->value) : $this->value == $item->getValue (), array ('class' => 'checkbox'), array ('data-prefix' => $this->prefix, 'data-name' => '[' . $this->name . '][]'));
      else
        foreach ($this->items as $item)
          $return .= form_checkbox ($this->prefix . '[' . $this->name . ']' . '[]', $item->getValue (), $item->getText (), is_array ($this->value) ? in_array ($item->getValue (), $this->value) : $this->value == $item->getValue (), array ('class' => 'checkbox'));
        
    $return .= '</div>';

    return $return;
  }
}