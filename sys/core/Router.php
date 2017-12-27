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

  public static function init ($routing = null) {
    self::setDirectories (array ());
    self::setMethod ('index');
    self::parseRoutes ();
  }
 
  public static function getRoutes () {
    return array (
        // '404' => 'main',
        'get' => array (
            '' => 'welcome',
            'main/c' => 'main@a',
            'admin/a/main/(:any)/b' => 'admin/', //'admin/a/main@a($1)'
          ),
        'post' => array (
            'admin/a/main/(:any)/b' => 'admin/a/main/a/$1', //'admin/a/main@a($1)'
          ),
        'put' => array (),
        'delete' => array (),
      );
  }

  private static function parseRoutes () {
    $uri = implode ('/', URL::segments ());

    $method = strtolower (request_is_cli () ? 'cli' : (isset ($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : (isset ($_POST['_method']) ? $_POST['_method'] : 'get')));
    $routes = self::getRoutes ();

    if (isset ($routes[$method]))
      foreach ($routes[$method] as $key => $controller) {
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
    $c = count ($segments = array_filter ($segments));

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
