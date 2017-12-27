<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2017, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class URL {
  private static $uriString;
  private static $segments;
  private static $rsegments;
  private static $baseUrl;

  public static function init () {
    self::$uriString = '';
    self::$baseUrl = null;
    self::$segments = self::$rsegments = array ();

    !request_is_cli () && config ('other', 'enable_query_strings') || self::setUriString (request_is_cli () ? self::parseArgv () : self::parseRequestUri ());
  }

  private static function setUriString ($str) {
    self::$uriString = trim (remove_invisible_characters ($str, false), '/');

    if (self::$uriString !== '') {
      self::$segments[0] = null;
      
      foreach (explode ('/', trim (self::$uriString, '/')) as $val)
        self::filterUri ($val = trim ($val)) && array_push (self::$segments, $val);

      unset (self::$segments[0]);
    }
  }

  private static function parseRequestUri () {
    if (!isset ($_SERVER['REQUEST_URI'], $_SERVER['SCRIPT_NAME']))
      return '';

    $uri = parse_url ('http://dummy' . $_SERVER['REQUEST_URI']);
    $query = isset ($uri['query']) ? $uri['query'] : '';
    $uri = urldecode (isset ($uri['path']) ? $uri['path'] : '');

    isset ($_SERVER['SCRIPT_NAME'][0]) && $uri = strpos ($uri, $_SERVER['SCRIPT_NAME']) === 0 ? (string) substr ($uri, strlen ($_SERVER['SCRIPT_NAME'])) : (strpos ($uri, dirname ($_SERVER['SCRIPT_NAME'])) === 0 ? (string) substr ($uri, strlen (dirname ($_SERVER['SCRIPT_NAME']))) : $uri);

    if (trim ($uri, '/') === '' && strncmp ($query, '/', 1) === 0 && ($query = explode ('?', $query, 2))) {
      $uri = $query[0];
      $_SERVER['QUERY_STRING'] = isset ($query[1]) ? $query[1] : '';
    } else {
      $_SERVER['QUERY_STRING'] = $query;
    }

    parse_str ($_SERVER['QUERY_STRING'], $_GET);

    if ($uri === '/' || $uri === '')
      return '/';

    return self::removeRelativeDirectory ($uri);
  }
  private static function parseArgv () {
    return ($args = array_slice ($_SERVER['argv'], 1)) ? implode ('/', $args) : '';
  }
  private static function removeRelativeDirectory ($uri) {
    $uris = array ();
    $tok = strtok ($uri, '/');

    while ($tok !== false) {
      if ((!empty ($tok) || $tok === '0') && $tok !== '..')
        array_push ($uris, $tok);
      $tok = strtok ('/');
    }

    return implode ('/', $uris);
  }
  public static function filterUri ($str) {
    $c = config ('other', 'permitted_uri_chars');

    if ($str && $c && !preg_match ('/^[' . $c . ']+$/i' . (UTF8_ENABLED ? 'u' : ''), $str))
      gg ('網址有不合法的字元！', 400);

    return $str;
  }
  public static function uriString () {
    return self::$uriString;
  }
  public static function segments () {
    return self::$segments;
  }
  public static function rsegments () {
    return self::$rsegments;
  }
  public static function setRsegments ($rsegments) {
    return self::$rsegments = $rsegments;
  }

  public static function currentUrl () {
    return self::baseUrl (self::uriString ());
  }
  public static function baseUrl () {
    $baseUrl =& self::$baseUrl;
    $baseUrl || ($baseUrl = config ('other', 'base_url')) || (isset ($_SERVER['HTTP_HOST']) && isset ($_SERVER['HTTP_HOST']) && ($baseUrl = (isset ($_SERVER['HTTPS']) && strtolower ($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http') . '://'. $_SERVER['HTTP_HOST'] . '/')) || gg ('尚未設定 base_url');

    if (!(($args = func_get_args ()) && ($args = ltrim (preg_replace ('/\/+/', '/', implode ('/', array_2d_to_1d ($args))), '/'))))
      return $baseUrl;

    return $baseUrl . $args;
  }
  public static function refresh ($args = '') {
    if (!(($args = func_get_args ()) && ($args = ltrim (preg_replace ('/\/+/', '/', implode ('/', array_2d_to_1d ($args))), '/'))))
      $args = '';

    header ('Refresh:0;url=' . self::baseUrl ($args));
    exit;
  }
  public static function redirect ($code = 302) {
    $args = func_get_args ();
    $code = array_shift ($args);

    if (!($args && ($args = ltrim (preg_replace ('/\/+/', '/', implode ('/', array_2d_to_1d ($args))), '/'))))
      $args = '';

    $code = !is_numeric ($code) ? isset ($_SERVER['SERVER_PROTOCOL'], $_SERVER['REQUEST_METHOD']) && $_SERVER['SERVER_PROTOCOL'] === 'HTTP/1.1' ? $_SERVER['REQUEST_METHOD'] !== 'GET' ? 303 : 307 : 302 : $code;

    header ('Location: ' . self::baseUrl ($args), true, $code);
    exit;
  }
}
