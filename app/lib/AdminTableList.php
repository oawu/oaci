<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class AdminTableListColumn {
  protected $sort, $class, $width, $title;
  
  public function __construct ($title = '') {
    $this->setTitle ($title)
         ->setSort ('');
  }

  public function setTd ($td) {
    $this->td = $td;
    return $this;
  }

  public function setSort ($sort) {
    $this->sort = $sort;
    return $this;
  }

  public function setClass ($class) {
    $this->class = $class;
    return $this;
  }

  public function setWidth ($width) {
    is_numeric ($width) && $this->width = $width;
    return $this;
  }

  public function setSwitch ($checked, $attrs = array ()) {
    return form_switch ('', '', $checked, $attrs);
  }

  public function setTitle ($title) {
    $this->title = $title;
    return $this;
  }

  public function thString ($sortUrl = '') {
    return '<th' . ($this->width ? ' width="' . $this->width . '"' : '') . '' . ($this->class ? ' class="' . $this->class . '"' : '') . '>' . AdminOrder::set ($this->title, $sortUrl ? '' : $this->sort) . '</th>';
  }

  public function tdString ($obj) {
    $td = $this->td;
    
    if (is_string ($td))
      $text = $td;

    if (is_callable ($td))
      $text = $td ($obj, $this);

    if (is_object ($text) && $text instanceof AdminTableListEditColumn && method_exists ($text, '__toString'))
      $text = (string)$text;

    return '<td' . ($this->width ? ' width="' . $this->width . '"' : '') . '' . ($this->class ? ' class="' . $this->class . '"' : '') . '>' . $text . '</td>';
  }

  public static function create ($title = '') {
    return new AdminTableListColumn ($title);
  }
}

class AdminTableListEditColumn extends AdminTableListColumn {
  protected $links = array ();

  public function __construct ($title = '') {
    parent::__construct ($title);
    $this->setWidth (78)
         ->setClass ('edit');
  }

  public function addEditLink ($url) {
    return $this->addLink ($url, array ('class' =>'icon-03'));
  }

  public function addDeleteLink ($url) {
    return $this->addLink ($url, array ('class' =>'icon-04', 'data-method' =>'delete'));
  }

  public function addShowLink ($url) {
    return $this->addLink ($url, array ('class' =>'icon-29'));
  }

  public function addLink ($url, $attrs = array ()) {
    $attrs['href'] = $url;
    array_push ($this->links, $attrs);
    return $this;
  }

  public function __toString () {
    return $this->toString ();
  }

  public function toString () {
    return implode ('', array_filter (array_map (function ($link) {
      $attr = implode (' ', array_map (function ($key, $val) { return $key . '="' . $val . '"'; }, array_keys ($link), array_values ($link)));
      return $attr ? '<a ' . $attr . '></a>' : null;
    }, $this->links)));
  }

  public static function create ($title = '') {
    return new AdminTableListEditColumn ($title);
  }
}

class AdminTableList {
  const KEY = '_s';
  private $objs, $columns, $sortUrl, $useSort = false;
  
  public function __construct ($objs = array ()) {
    $this->setObjs ($objs);
    $this->columns = array ();
    $this->sortUrl = '';
  }

  public function prependClomun (AdminTableListColumn $column) {
    array_unshift ($this->columns, $column);
    return $this;
  }
  public function appendClomun (AdminTableListColumn $column) {
    array_push ($this->columns, $column);
    return $this;
  }

  public function isUseSort () {
    return $this->useSort;
  }
  public function setSortUrl ($url) {
    $this->useSort = true;

    if ($url && ($get = Input::get (AdminTableList::KEY)) === 'true' && ($this->sortUrl = $url))
      $this->prependClomun (AdminTableListColumn::create ('排序')->setWidth (44)->setClass ('center')->setTd ('<span class="icon-01 drag"></span>'));
    return $this;
  }
  public function setObjs ($objs) {
    $this->objs = $objs;
    return $this;
  }

  public static function create () {
    if (!$args = func_get_args ())
      return new AdminTableList ();

    $instance = is_array ($objs = array_shift ($args)) ? new AdminTableList ($objs) : new AdminTableList ();
    
    foreach ($args as $arg)
      $instance->appendClomun ($arg);

    return $instance;
  }

  public function __toString () {
    return $this->toString ();
  }

  public function toString () {
    $return = '';
    $sortUrl = $this->sortUrl;

    if ($sortUrl)
      $return .= '<table class="list dragable" data-sorturl="' . $sortUrl . '">';
    else
      $return .= '<table class="list">';

      $return .= '<thead>';
        $return .= '<tr>';
        $return .= implode ('', array_map (function ($column) use ($sortUrl) { return $column->thString ($sortUrl); }, $this->columns));
        $return .= '</tr>';
      $return .= '</thead>';
      $return .= '<tbody>';
      // 
      if (!$this->objs)
        $return .= '<tr><td colspan="' . count ($this->columns) . '"></td></tr>';
      else
        $return .= implode ('', array_map (function ($obj) use ($sortUrl) {
          return ($sortUrl && isset ($obj->id, $obj->sort) ? '<tr data-id="' . $obj->id . '" data-sort="' . $obj->sort . '">' : '<tr>') . implode ('', array_map (function ($column) use ($obj) { 
            $column = clone $column;
            return $column->tdString ($obj); }, $this->columns)) . '</tr>';
        }, $this->objs));


      $return .= '</tbody>';
    $return .= '</table>';

    return $return;
  }
}