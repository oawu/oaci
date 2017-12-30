<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class Restful {
  private static $list = array ();
  private static $urls = null;

  public static function addGroup ($controller, $action, $format) {
    isset (self::$list[$controller]) || self::$list[$controller] = array ();
    self::$list[$controller][$action] = $format;
  }
  public static function exception ($err) {
    throw new Exception ($err);
  }
  public static function setUrls ($key, $parents) {
    if (!isset (self::$list[$key]))
      throw new Exception ('Restful 設定 setUrls 錯誤！');

    $parents = array_orm_column ($parents, 'id');

    self::$urls = array_map (function ($t) use ($parents) {
      $i = -1;
      return preg_replace_callback ('/\(\[0\-9\]\+\)/', function ($matches) use ($parents, &$i) {
        return isset ($parents[++$i]) ? $parents[$i] : '%s';
      }, $t);
    }, self::$list[$key]);
  }

  public static function __callStatic ($name, $arguments) {
    if (in_array ($name, array ('index', 'add', 'create')))
      if (isset (self::$urls[$name]))
        return URL::base (self::$urls[$name]);
      else
        throw new Exception ('Restful 錯誤，尚未設定 setUrls');

    if (in_array ($name, array ('show', 'edit', 'update', 'destroy')))
      if ($arguments = array_shift ($arguments))
        if ((is_object ($arguments) && isset ($arguments->id)) || is_string ($arguments) || is_numeric ($arguments))
          return URL::base (sprintf (self::$urls[$name], is_object ($arguments) ? $arguments->id : $arguments));
        else
          throw new Exception ('Restful::' . $name . '() 予物件或 ID 格式錯誤');
      else
        throw new Exception ('Restful::' . $name . '() 請給予物件或 ID');
  }
}