<?php defined ('BASEPATH') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2017, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class Exceptions {
  private static $obLevel;
  private static $clean;

  public static function init () {
    self::$clean = false;
    self::$obLevel = ob_get_level ();
  }

  public static function logException ($severity, $message, $filepath, $line) {
    class_exists ('Log', false) && Log::message ('Severity: ' . (($t = config ('exceptions', 'levels', $severity)) ? $t : $severity) . ' --> ' . $message . ' ' . $filepath . ' ' . $line);
  }

  public static function setStatusHeader ($code = 200, $text = null) {
    if (request_is_cli ())
      return ;

    if (!($code && is_numeric ($code)))
      self::showError ('Status Codes 必須要是數字。', 500);

    if (!(($text && is_string ($text)) || ($text = config ('exceptions', 'stati', $code))))
      self::showError ('沒有訊息，請確認您的 Status Codes 是否正確。', 500);

    if (strpos (PHP_SAPI, 'cgi') === 0)
      return header ('Status: ' . $code . ' ' . $text, true);

    $serverProtocol = isset ($_SERVER['SERVER_PROTOCOL']) && in_array ($_SERVER['SERVER_PROTOCOL'], array ('HTTP/1.0', 'HTTP/1.1', 'HTTP/2'), true) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1';
    header ($serverProtocol . ' ' . $code . ' ' . $text, true, $code);
  }

  public static function showError ($message, $statusCode = 500, $heading = '') {
    self::setStatusHeader ($statusCode);

    if (ob_get_level () > self::$obLevel + 1)
      ob_end_flush ();

    self::$clean && ob_get_contents () && @ob_end_clean ();

    ob_start ();
    echo request_is_cli ()
         ? ' -> ' . (is_array ($message) ? implode ("\n\t", $message) : $message) . "\n"
         : ($heading ? '<title>' . $heading . '</title>' : '') . '<p>' . (is_array ($message) ? implode ('</p><p>', $message) : $message) . '</p>';
    $buffer = ob_get_contents ();
    @ob_end_clean ();

    echo $buffer;
    exit ($statusCode);
  }

  public static function show404 ($page = '', $logError = false) {
    if (request_is_cli ()) {
      $heading = 'Not Found';
      $message = '找不到此 controller 或 method。';
    } else {
      $heading = '此頁面不存在';
      $message = '找不到您的頁面(404)。';
    }

    $logError && class_exists ('Log', false) && Log::message ($heading . ': ' . $page);

    self::showError ($message, 404, $heading);
  }

  public static function showException ($exception) {
    $message = $exception->getMessage ();
    
    if (!$message)
      $message = '(null)';

    if (ob_get_level () > self::$obLevel + 1)
      ob_end_flush ();

    self::$clean && ob_get_contents () && @ob_end_clean ();

    ob_start ();

    echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" />' . '<br/>';
    echo 'An uncaught Exception was encountered' . '<br/>';
    echo 'Type:        ' . get_class ($exception) . '<br/>';
    echo 'Message:     ' . $message . '<br/>';
    echo 'Filename:    ' . $exception->getFile () . '<br/>';
    echo 'Line Number: ' . $exception->getLine () . '<br/>';

    if (defined ('SHOW_DEBUG_BACKTRACE') && SHOW_DEBUG_BACKTRACE === true) {
      echo '' . '<br/>';
      echo 'Backtrace:' . '<br/>';
      foreach ($exception->getTrace () as $error) {
        if (isset($error['file']) && strpos($error['file'], realpath(BASEPATH)) !== 0) {
          echo 'File: ' . $error['file'] . '<br/>';
          echo 'Line: ' . $error['line'] . '<br/>';
          echo 'Function: ' . $error['function'] . '<br/>';
        }
      }
    }

    $buffer = ob_get_contents ();
    @ob_end_clean ();
    echo $buffer;
    exit ();
  }

  public static function showPhpError ($severity, $message, $filepath, $line) {
    if (ob_get_level () > self::$obLevel + 1)
      ob_end_flush ();

    self::$clean && ob_get_contents () && @ob_end_clean ();

    ob_start ();

    echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" />' . '<br/>';
    echo '嚴重性：' . (($t = config ('exceptions', 'levels', $severity)) ? $t : $severity) . '<br/>';
    echo '錯誤訊息：' . $message . '<br/>';
    echo '位置：' . $filepath . '<br/>';
    echo '行數：' . $line . '<br/>';

    $buffer = ob_get_contents ();
    @ob_end_clean ();

    echo $buffer;
    // exit ();
  }
}

if (!function_exists ('_error_handler')) {
  function _error_handler ($severity, $message, $filepath, $line) {
    $isError = (((E_ERROR | E_PARSE | E_COMPILE_ERROR | E_CORE_ERROR | E_USER_ERROR) & $severity) === $severity);

    if ($isError)
      Exceptions::setStatusHeader (500);

    if (($severity & error_reporting ()) !== $severity)
      return;

    Exceptions::logException ($severity, $message, $filepath, $line);

    if (str_ireplace (array ('off', 'none', 'no', 'false', 'null'), '', ini_get ('display_errors')))
      Exceptions::showPhpError ($severity, $message, $filepath, $line);

    if ($isError)
      exit(1);
  }
}

if (!function_exists ('_exception_handler')) {
  function _exception_handler ($exception) {
    Exceptions::logException ('Error', 'Exception: ' . $exception->getMessage (), $exception->getFile (), $exception->getLine ());

    request_is_cli () || Exceptions::setStatusHeader (500);

    if (str_ireplace (array ('off', 'none', 'no', 'false', 'null'), '', ini_get ('display_errors')))
      Exceptions::showException ($exception);

    exit(1);
  }
}

if (!function_exists ('_shutdown_handler')) {
  function _shutdown_handler () {
    $lastError = error_get_last ();
    
    if (isset ($lastError) && ($lastError['type'] & (E_ERROR | E_PARSE | E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_COMPILE_WARNING)))
      _error_handler ($lastError['type'], $lastError['message'], $lastError['file'], $lastError['line']);
  }
}

/* ------------------------------------------------------
 *  定義自己的 Error Handler
 * ------------------------------------------------------ */
set_error_handler ('_error_handler');
set_exception_handler ('_exception_handler');
register_shutdown_function ('_shutdown_handler');
