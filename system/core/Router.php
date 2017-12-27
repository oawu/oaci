<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

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
    $uri = implode ('/', URI::segments ());

    $method = getServerMethodRequest ();
    $routes = self::getRoutes ();

    if (isset ($routes[$method]))
      foreach ($routes[$method] as $key => $controller) {
        $key = str_replace (array (':any', ':num'), array ('[^/]+', '[0-9]+'), $key);

        if (preg_match ('#^' . $key . '$#', $uri, $matches)) {
          if (strpos ($controller, '$') !== false && strpos ($key, '(') !== false)
            $controller = preg_replace ('#^' . $key . '$#', $controller, $uri);

          self::setRequest (explode ('/', $controller));
          return;
        }
      }

    self::setRequest (array_values (URI::segments ()));
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

    URI::setRsegments ($segments);
  }
  private static function validateRequest ($segments) {
    $c = count ($segments = array_filter ($segments));

    while ($c-- > 0)
      if (($test = self::getDirectory () . str_replace ('-', '_', $segments[0])) && !file_exists (APPPATH . 'controllers' . DIRECTORY_SEPARATOR . $test . '.php') && is_dir (APPPATH . 'controllers' . DIRECTORY_SEPARATOR . self::getDirectory () . $segments[0]) && self::appendDirectory (array_shift ($segments)))
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



  // private static function setDefaultController () {
  //     return;
  //   // echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
  //   // var_dump (self::getDirectory ());
  //   // exit ();

  //   // if (empty($this->default_controller))
  //   // {
  //     // show_error('您沒有設定預設網頁。');
  //   // }

  //   // Is the method being specified?
  //   // if (sscanf($this->default_controller, '%[^/]/%s', $class, $method) !== 2)
  //   // {
  //   //   $method = 'index';
  //   // }

  //   // if ( ! file_exists(APPPATH.'controllers/'.self::$directories.ucfirst($class).'.php'))
  //   // {
  //   //   // This will trigger 404 later
  //   // }

  //   $this->set_class($class);
  //   $this->set_method($method);

  //   // Assign routed segments, index starting from 1
  //   $this->uri->rsegments = array(
  //     1 => $class,
  //     2 => $method
  //   );

  //   log_message('debug', 'No URI present. Default controller set.');
  // }









  // private static function setRouting () {

  //   // if (file_exists(APPPATH.'config/routes.php'))
  //   // {
  //   //   include(APPPATH.'config/routes.php');
  //   // }

  //   // if (file_exists(APPPATH.'config/'.ENVIRONMENT.'/routes.php'))
  //   // {
  //   //   include(APPPATH.'config/'.ENVIRONMENT.'/routes.php');
  //   // }

  //   // // Validate & get reserved routes
  //   // if (isset($route) && is_array($route))
  //   // {
  //   //   isset($route['default_controller']) && $this->default_controller = $route['default_controller'];
  //   //   isset($route['translate_uri_dashes']) && $this->translate_uri_dashes = $route['translate_uri_dashes'];
  //   //   unset($route['default_controller'], $route['translate_uri_dashes']);
  //   //   $this->routes = $route;
  //   // }

  //   // Are query strings enabled in the config file? Normally CI doesn't utilize query strings
  //   // since URI segments are more search-engine friendly, but they can optionally be used.
  //   // If this feature is enabled, we will gather the directory/class/method a little differently

  //   // if (!isCli () && Config::get ('general', 'enable_query_strings') === true) {
  //   //   // If the directory is set at this time, it means an override exists, so skip the checks
  //   //   if ( ! isset(self::$directories))
  //   //   {
  //   //     $_d = Config::get ('general', 'directory_trigger');
  //   //     $_d = isset($_GET[$_d]) ? trim($_GET[$_d], " \t\n\r\0\x0B/") : '';

  //   //     if ($_d !== '')
  //   //     {
  //   //       $this->uri->filter_uri($_d);
  //   //       $this->setDirectories($_d);
  //   //     }
  //   //   }

  //   //   $_c = trim(Config::get ('general', 'controller_trigger'));
  //   //   if ( ! empty($_GET[$_c]))
  //   //   {
  //   //     $this->uri->filter_uri($_GET[$_c]);
  //   //     $this->set_class($_GET[$_c]);

  //   //     $_f = trim(Config::get ('general', 'function_trigger'));
  //   //     if ( ! empty($_GET[$_f]))
  //   //     {
  //   //       $this->uri->filter_uri($_GET[$_f]);
  //   //       $this->set_method($_GET[$_f]);
  //   //     }

  //   //     $this->uri->rsegments = array(
  //   //       1 => $this->class,
  //   //       2 => $this->method
  //   //     );
  //   //   }
  //   //   else
  //   //   {
  //   //     $this->_set_default_controller();
  //   //   }

  //   //   // Routing rules don't apply to query strings and we don't need to detect
  //   //   // directories, so we're done here
  //   //   return;
  //   // }

  //   // Is there anything to parse?

  //   // if (URI::uriString () !== '')
  //   // else
  //     // self::setDefaultController ();
  // }