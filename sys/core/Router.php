<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2017, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class Router {
  private static $directories;
  private static $class;
  private static $method;
  private static $routers = array ();

  public static function init ($routing = null) {
    self::$routers = array ();
    self::setDirectories (array ());
    self::setMethod ('index');
    self::setRoutes ();
    self::parseRoutes ();
  }
 
  public static function group ($prefix, $callback) {
    $callback ();
  }
  private static function _getGroup () {
    if (($group = array_filter (array_map (function ($trace) {
                  return isset ($trace['class']) && ($trace['class'] == 'Router') && isset ($trace['function']) && ($trace['function'] == 'group') && isset ($trace['type']) && ($trace['type'] == '::') && isset ($trace['args'][0]) ? $trace['args'][0] : null;
                }, debug_backtrace (DEBUG_BACKTRACE_PROVIDE_OBJECT)))) && ($group = array_shift ($group)))
      return trim ($group, '/') . '/';
    else
      return '';    
  }
  public static function resource ($uris, $controller) {
    is_array ($uris) || $uris = array ($uris);
    $c = count ($uris);

    $group = self::_getGroup ();
    $prefix = trim ($group, '/') . '/';

    $t1 = $c > 1 ? ', ' . implode (', ', array_map (function ($a) { return '$' . $a; }, range (1, $c - 1))) : '';
    $t2 = $c > 1 ? ', ' . implode (', ', array_map (function ($a) { return '$' . $a; }, range (2, $c))) : '';

    self::get    ($prefix . implode ('/(:num)/', $uris) . '/',                 $prefix . $controller . '@index(' . $t1 . ')');
    self::get    ($prefix . implode ('/(:num)/', $uris) . '/(:num)',           $prefix . $controller . '@show($1' . $t2 . ')');
    self::get    ($prefix . implode ('/(:num)/', $uris) . '/add',              $prefix . $controller . '@add(' . $t1 . ')');
    self::post   ($prefix . implode ('/(:num)/', $uris) . '/',                 $prefix . $controller . '@create(' . $t1 . ')');
    self::get    ($prefix . implode ('/(:num)/', $uris) . '/(:num)' . '/edit', $prefix . $controller . '@edit($1' . $t2 . ')');
    self::put    ($prefix . implode ('/(:num)/', $uris) . '/(:num)',           $prefix . $controller . '@update($1' . $t2 . ')');
    self::delete ($prefix . implode ('/(:num)/', $uris) . '/(:num)',           $prefix . $controller . '@destroy($1' . $t2 . ')');
  }
  private static function method ($m, $params) {
    (($uri = array_shift ($params)) === null || ($params = array_shift ($params)) === null) && gg ('設定 Router 錯誤。');

    $params = preg_split ('/[@,\(\)\s]+/', $params);
    $controller = array_shift ($params);
    $method = array_shift ($params);
    $params = array_filter ($params, function ($param) { return $param !== null && $param !== ''; });

    isset (self::$routers[$m]) || self::$routers[$m] = array ();
    self::$routers[$m][trim ($uri, '/')] = $controller . ($method !== null ? '/' . $method : '') . ($params !== null ? '/' . implode ('/', $params) : '');
  }

  public static function __callStatic ($name, $arguments) {
    if (in_array ($name, array ('get', 'post', 'put', 'delete', 'cli')))
      return self::method ($name, $arguments);
  }

  public static function setRoutes () {
    Load::file (APPPATH . 'config' . DIRECTORY_SEPARATOR . 'router.php', true);
  }

  private static function parseRoutes () {
    $uri = implode ('/', URL::segments ());

    $method = strtolower (request_is_cli () ? 'cli' : (isset ($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : (isset ($_POST['_method']) ? $_POST['_method'] : 'get')));

    if (isset (self::$routers[$method]))
      foreach (self::$routers[$method] as $key => $controller) {
        $key = str_replace (array (':any', ':num'), array ('[^/]+', '[0-9]+'), $key);

        if (preg_match ('#^' . $key . '$#', $uri, $matches)) {
          strpos ($controller, '$') !== false && strpos ($key, '(') !== false && $controller = preg_replace ('#^' . $key . '$#', $controller, $uri);
          self::setRequest (explode ('/', $controller));
          return;
        }
      }

    self::setRequest (array_values (URL::segments ()));
  }
  private static function setRequest ($segments = array ()) {
    if (!$segments = self::validateRequest ($segments))
      return ;

    $segments[0] = str_replace ('-', '_', $segments[0]);
    $segments[1] = isset ($segments[1]) ? str_replace ('-', '_', $segments[1]) : 'index';

    self::setClass ($segments[0]);
    self::setMethod ($segments[1]);

    array_unshift ($segments, null);
    unset($segments[0]);

    URL::setRsegments ($segments);
  }
  private static function validateRequest ($segments) {
    $c = count ($segments = array_filter ($segments, function ($segment) {
      return $segment !== null && $segment !== '';
    }));

    while ($c-- > 0)
      if (($test = self::getDirectory () . str_replace ('-', '_', $segments[0])) && !file_exists (APPPATH . 'controller' . DIRECTORY_SEPARATOR . $test . EXT) && is_dir (APPPATH . 'controllers' . DIRECTORY_SEPARATOR . self::getDirectory () . $segments[0]) && self::appendDirectory (array_shift ($segments)))
        continue;
      else
        return $segments;

    return $segments;
  }
  public static function getDirectory () {
    return implode (DIRECTORY_SEPARATOR, self::$directories) . (self::$directories ? DIRECTORY_SEPARATOR : '');
  }
  public static function appendDirectory ($dir) {
    return array_push (self::$directories, $dir);
  }
  public static function setDirectories ($dirs) {
    self::$directories = $dirs;
  }
  public static function setClass ($class) {
    return self::$class = $class;
  }
  public static function setMethod ($method) {
    return self::$method = $method;
  }
  public static function getClass () {
    return self::$class;
  }
  public static function getMethod () {
    return self::$method;
  }
}
