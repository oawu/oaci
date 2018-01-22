<?php

namespace AdminLib;

defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class Search {
  const KEY = '_q';

  private $titles = array ();
  private $counter = 0;
  private $where = array ();
  private $searches = array ();

  public function __construct (&$where = null) {
    $where !== null || $where = Where::create ();

    $this->where = $where;
    $this->counter = 0;
    $this->titles = array ();
    $this->searches = array ();
  }

  private function add ($key) {
    $value = \Input::get ($key, true);

    if ($value === null || $value === '' || (is_array ($value) && !count ($value)) || empty ($this->searches[$key]['sql']))
      return $this;
    
    is_callable ($this->searches[$key]['sql']) && $this->searches[$key]['sql'] = $this->searches[$key]['sql']($value);
    
    is_string ($this->searches[$key]['sql']) && $this->where->and ($this->searches[$key]['sql'], strpos (strtolower ($this->searches[$key]['sql']), ' like ') !== false ? '%' . $value . '%' : $value);
    is_object ($this->searches[$key]['sql']) && $this->searches[$key]['sql'] instanceof Where && $this->where->and ($this->searches[$key]['sql']);

    $this->searches[$key]['value'] = $value;
    array_push ($this->titles, $this->searches[$key]['title']);
    return $this;
  }
  public function input ($title, $sql, $type = 'text') {
    $this->searches[$key = Search::KEY . ($this->counter++)] = array ('el' => 'input', 'title' => $title, 'sql' => $sql, 'type' => $type);
    return $this->add ($key);
  }

  public function select ($title, $sql, $options) {
    $this->searches[$key = Search::KEY . ($this->counter++)] = array ('el' => 'select', 'title' => $title, 'sql' => $sql, 'options' => $options);
    return $this->add ($key);
  }
  
  public function checkboxs ($title, $sql, $items) {
    $this->searches[$key = Search::KEY . ($this->counter++)] = array ('el' => 'checkboxs', 'title' => $title, 'sql' => $sql, 'items' => $items);
    return $this->add ($key);
  }
  
  public function radios ($title, $sql, $items) {
    $this->searches[$key = Search::KEY . ($this->counter++)] = array ('el' => 'radios', 'title' => $title, 'sql' => $sql, 'items' => $items);
    return $this->add ($key);
  }
  
  private function conditions () {
    $gets = \Input::get ();

    $return = '<div class="conditions">';
      
      $return .= implode ('', array_map (function ($key, $condition) use (&$gets) {
        unset ($gets[$key]);
        $return = '';

        if (!(isset ($condition['el']) && in_array ($condition['el'], array ('input', 'select', 'checkboxs', 'radios'))))
          return $return;

        switch ($condition['el']) {
          case 'input':
            if (!isset ($condition['title']))
              return $return;

            $return .= '<label class="row">';
            $return .= '<b>依據' . $condition['title'] . '搜尋</b>';
            $return .= '<input name="' . $key . '" type="' . (isset ($condition['type']) ? $condition['type'] : 'text') . '" placeholder="依據' . $condition['title'] . '關鍵字搜尋" value="' . (empty ($condition['value']) ? '' : $condition['value']) . '" />';
            $return .= '</label>';
            break;
          
          case 'select':
            if (!isset ($condition['title'], $condition['options']))
              return $return;

            $return .= '<label class="row">';
            $return .= '<b>依據' . $condition['title'] . '搜尋</b>';
            $return .= '<select name="' . $key . '">';
            $return .= '<option value="">依據' . $condition['title'] . '搜尋</option>';
            $return .= implode ('', array_map (function ($option) use ($condition) { return $option && isset ($option['value'], $option['text']) ? '<option value="' . $option['value'] . '"' . (!empty ($condition['value']) && $condition['value'] == $option['value'] ? ' selected' : '') . '>' . $option['text'] . '</option>' : ''; }, $condition['options']));
            $return .= '</select>';
            $return .= '</label>';
            break;
          
          case 'checkboxs':
            if (!isset ($condition['title'], $condition['items']))
              return $return;

            $return .= '<div class="row">';
            $return .= '<b>依據' . $condition['title'] . '搜尋</b>';
            $return .= '<div class="checkboxs">';
            $return .= implode ('', array_map (function ($option) use ($condition, $key) { return $option && isset ($option['value'], $option['text']) ? '<label><input type="checkbox" name="' . $key . '[]" value="' . $option['value'] . '"' . (!empty ($condition['value']) && (is_array ($condition['value']) ? in_array ($option['value'], $condition['value']) : $condition['value'] == $option['value']) ? ' checked' : '') . ' /><span></span>' . $option['text'] . '</label>' : ''; }, $condition['items']));
            $return .= '</div>';
            $return .= '</div>';
            break;

          case 'radios':
            if (!isset ($condition['title'], $condition['items']))
              return $return;

            $return .= '<div class="row">';
            $return .= '<b>依據' . $condition['title'] . '搜尋</b>';
            $return .= '<div class="radios">';
            $return .= implode ('', array_map (function ($option) use ($condition, $key) { return $option && isset ($option['value'], $option['text']) ? '<label><input type="radio" name="' . $key . '" value="' . $option['value'] . '"' . (!empty ($condition['value']) && $condition['value'] == $option['value'] ? ' checked' : '') . ' /><span></span>' . $option['text'] . '</label>' : ''; }, $condition['items']));
            $return .= '</div>';
            $return .= '</div>';
            break;
          
          default:
            return $return;
            break;
        }

        return $return;
      }, array_keys ($this->searches), array_values ($this->searches)));

      $gets = http_build_query ($gets);
      $gets && $gets = '?' . $gets;

      $return .= '<div class="btns">';
        $return .= '<button type="submit">搜尋</button>';
        $return .= '<a href="' . \URL::current () . $gets . '">取消</a>';
      $return .= '</div>';
    $return .= '</div>';

    return $return;
  }

  public function renderForm ($total, $add = '', Table $sortKey) {
    if ($sortKey->isUseSort ()) {
      $gets = \Input::get ();

      if (isset ($gets[Order::KEY]))
        unset ($gets[Order::KEY]);

      foreach (array_keys ($this->searches) as $key)
        if (isset ($gets[$key]))
          unset ($gets[$key]);
  
      if (isset ($gets[Table::KEY]) && $gets[Table::KEY] === 'true') {
        $ing = false;
        unset ($gets[Table::KEY]);
      } else {
        $ing = true;
        $gets[Table::KEY] = 'true';
      }

      $gets = http_build_query ($gets);
      $gets && $gets = '?' . $gets;
      $sortKey = \URL::current () . $gets;
    } else {
      $sortKey = '';
    }

    $return = '<form class="search" action="' . \RestfulUrl::index () . '" method="get">';
      $return .= '<div class="info' . ($this->titles ? ' show' : '') . '">';
        $return .= '<a class="icon-13 conditions-btn"></a>';

        $return .= '<span>' . ($add ? '<a href="' . $add . '" class="icon-07">新增</a>' : '') . ($sortKey ? '<a href="' . $sortKey . '" class="icon-' . ($ing ? '41' : '18') . '">' . ($ing ? '排序' : '完成') . '</a>' : '') . '</span>';
        $return .= '<span>' . ($this->titles ? '您針對' . implode ('、', array_map (function ($title) { return '「' . $title . '」'; }, $this->titles)) . '搜尋，結果' : '目前全部') . '共有 <b>' . number_format ($total) . '</b> 筆。' . '</span>';
      $return .= '</div>';
      $return .= $this->conditions ();
    $return .= '</form>';
    return $return;
  }

  public static function create (&$where = null) {
    return new Search ($where);
  }
}