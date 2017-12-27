<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2017, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class WhereBuilder {
  private $where = array ();

  public function __construct ($where = array ()) {
    $this->where = $where;
  }

  public function toString () {
    return call_user_func_array ('sprintf', preg_replace ('/\?/', '%s', $this->where));
  }
  public function toArray () {
    return $this->where;
  }

  public static function create () {
    if (!$args = func_get_args ())
      return new WhereBuilder (array ());

    $where = array_shift ($args);

    if (is_string ($where))
      $where = call_user_func_array (array ('self', 'andWhere'), array_merge (array (array ()), array ($where), $args));

    return new WhereBuilder ($where);
  }

  public function __call ($name, $arguments) {
    switch ($name) {
      case 'andWhere':
        $this->where = call_user_func_array (array ('self', '_andWhere'), array_merge (array ($this->where), $arguments));
        break;

      case 'orWhere':
      $this->where || $this->where = array (array_shift ($arguments));
        $this->where = call_user_func_array (array ('self', '_orWhere'), array_merge (array ($this->where), $arguments));
        break;
    }
    return $this;
  }

  public static function _andWhere () {
    if (!$args = func_get_args ())
      return array ();

    $where = array_shift ($args);
    if (is_string ($where))
      return call_user_func_array (array ('self', 'andWhere'), array_merge (array (array ()), array ($where), $args));

    $str = array_shift ($args);
    if (count ($args) < ($c = substr_count ($str, '?')))
      throw new Exception ('參數錯誤。「' . $str . '」 有 ' . $c . ' 個參數，目前只給 ' . count ($args) . ' 個。');

    if (!$where)
      $where[0] = '(' . $str . ')';
    else
      $where[0] = '(' . $where[0] . ')' . ' AND (' . $str . ')';

    foreach (array_splice ($args, 0, $c) as $arg)
      $arg === null || array_push ($where, $arg);
    
    return $where;
  }
  public static function _orWhere () {
    if (!$args = func_get_args ())
      return array ();

    $where = array_shift ($args);
    if (is_string ($where))
      return call_user_func_array (array ('self', 'andWhere'), array_merge (array (array ()), array ($where), $args));

    $str = array_shift ($args);
    if (count ($args) < ($c = substr_count ($str, '?')))
      throw new Exception ('參數錯誤。「' . $str . '」 有 ' . $c . ' 個參數，目前只給 ' . count ($args) . ' 個。');

    if (!$where)
      $where[0] = '(' . $str . ')';
    else
      $where[0] = '(' . $where[0] . ')' . ' OR (' . $str . ')';

    foreach (array_splice ($args, 0, $c) as $arg)
      $arg === null || array_push ($where, $arg);
    
    return $where;
  }
  public static function __callStatic ($name, $arguments) {
    switch ($name) {
      case 'andWhere':
        return call_user_func_array (array ('self', '_andWhere'), $arguments);
        break;

      case 'orWhere':
        return call_user_func_array (array ('self', '_orWhere'), $arguments);
        break;
      
      default:
        break;
    }
  }
}