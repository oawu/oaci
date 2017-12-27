<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

class Exceptions {
  private static $obLevel;
  private static $stati = array (100 => 'Continue', 101 => 'Switching Protocols', 200 => 'OK', 201 => 'Created', 202 => 'Accepted', 203 => 'Non-Authoritative Information', 204 => 'No Content', 205 => 'Reset Content', 206 => 'Partial Content', 300 => 'Multiple Choices', 301 => 'Moved Permanently', 302 => 'Found', 303 => 'See Other', 304 => 'Not Modified', 305 => 'Use Proxy', 307 => 'Temporary Redirect', 400 => 'Bad Request', 401 => 'Unauthorized', 402 => 'Payment Required', 403 => 'Forbidden', 404 => 'Not Found', 405 => 'Method Not Allowed', 406 => 'Not Acceptable', 407 => 'Proxy Authentication Required', 408 => 'Request Timeout', 409 => 'Conflict', 410 => 'Gone', 411 => 'Length Required', 412 => 'Precondition Failed', 413 => 'Request Entity Too Large', 414 => 'Request-URI Too Long', 415 => 'Unsupported Media Type', 416 => 'Requested Range Not Satisfiable', 417 => 'Expectation Failed', 422 => 'Unprocessable Entity', 426 => 'Upgrade Required', 428 => 'Precondition Required', 429 => 'Too Many Requests', 431 => 'Request Header Fields Too Large', 500 => 'Internal Server Error', 501 => 'Not Implemented', 502 => 'Bad Gateway', 503 => 'Service Unavailable', 504 => 'Gateway Timeout', 505 => 'HTTP Version Not Supported', 511 => 'Network Authentication Required');
  public static $levels = array (E_ERROR => 'Error', E_WARNING => 'Warning', E_PARSE => 'Parsing Error', E_NOTICE => 'Notice', E_CORE_ERROR => 'Core Error', E_CORE_WARNING => 'Core Warning', E_COMPILE_ERROR => 'Compile Error', E_COMPILE_WARNING => 'Compile Warning', E_USER_ERROR => 'User Error', E_USER_WARNING => 'User Warning', E_USER_NOTICE => 'User Notice', E_STRICT => 'Runtime Notice');

  public static function init () {
    self::$obLevel = ob_get_level ();
  }

  public static function logException ($severity, $message, $filepath, $line) {
    $severity = isset(self::$levels[$severity]) ? self::$levels[$severity] : $severity;
    Log::message ('Severity: ' . $severity . ' --> ' . $message . ' ' . $filepath . ' ' . $line);
  }

  public static function setStatusHeader ($code = 200, $text = '') {
    if (isCli ()) return ;

    if (empty ($code) || !is_numeric ($code))
      self::showError ('Status codes must be numeric', 500);

    if (!$text) {
      if (!isset (self::$stati[$code]))
        self::showError ('No status text available. Please check your status code number or supply your own message text.', 500);
      $text = self::$stati[$code];
    }

    if (strpos (PHP_SAPI, 'cgi') === 0) {
      header ('Status: ' . $code . ' ' . $text, true);
      return;
    }

    $serverProtocol = isset($_SERVER['SERVER_PROTOCOL']) && in_array ($_SERVER['SERVER_PROTOCOL'], array ('HTTP/1.0', 'HTTP/1.1', 'HTTP/2'), true) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1';
    header ($serverProtocol.' '.$code.' '.$text, true, $code);
  }

  public static function showError ($message, $statusCode = 500) {
    if (isCli ()) {
      $message = " -> " . (is_array ($message) ? implode ("\n\t", $message) : $message);
    } else {
      self::setStatusHeader ($statusCode);
      $message = '<p>' . (is_array ($message) ? implode ('</p><p>', $message) : $message) . '</p>';
    }

    if (ob_get_level () > self::$obLevel + 1) ob_end_flush ();

    ob_start ();
    echo $message;
    $buffer = ob_get_contents();
    ob_end_clean();

    echo $buffer;
    exit ($statusCode);
  }

  public static function show404 ($page = '', $logError = false) {
    if (isCli ()) {
      $heading = 'Not Found';
      $message = '找不到此 controller/method。';
    } else {
      $heading = '此頁面不存在(404)';
      $message = '找不到您的頁面。';
    }

    if ($logError) Log::message ($heading . ': ' . $page);

    self::showError ($message, 404);
  }

  public static function showException ($exception) {
    $message = $exception->getMessage ();
    
    if (empty($message))
      $message = '(null)';

    if (ob_get_level () > self::$obLevel + 1) ob_end_flush ();

    ob_start();

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

    $buffer = ob_get_contents();
    ob_end_clean();
    echo $buffer;
  }

  public static function showPhpError ($severity, $message, $filepath, $line) {
    $severity = isset (self::$levels[$severity]) ? self::$levels[$severity] : $severity;
    if (ob_get_level () > self::$obLevel + 1) ob_end_flush ();

    ob_start();

    echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" />' . '<br/>';
    echo '嚴重性：' . $severity . '<br/>';
    echo '錯誤訊息：' . $message . '<br/>';
    echo '位置：' . $filepath . '<br/>';
    echo '行數：' . $line . '<br/>';

    $buffer = ob_get_contents();
    ob_end_clean();
    
    echo $buffer;
  }
}

if (!function_exists ('_errorHandler')) {
  function _errorHandler ($severity, $message, $filepath, $line) {
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

if (!function_exists ('_exceptionHandler')) {
  function _exceptionHandler ($exception) {
    Exceptions::logException ('Error', 'Exception: ' . $exception->getMessage (), $exception->getFile (), $exception->getLine ());

    isCli() OR Exceptions::setStatusHeader (500);

    if (str_ireplace (array ('off', 'none', 'no', 'false', 'null'), '', ini_get ('display_errors')))
      Exceptions::showException ($exception);

    exit(1);
  }
}

if (!function_exists ('_shutdownHandler')) {
  function _shutdownHandler () {
    $lastError = error_get_last ();
    
    if (isset ($lastError) && ($lastError['type'] & (E_ERROR | E_PARSE | E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_COMPILE_WARNING)))
      _errorHandler ($lastError['type'], $lastError['message'], $lastError['file'], $lastError['line']);
  }
}

/*
 * ------------------------------------------------------
 *  定義自己的 Error Handler
 * ------------------------------------------------------
 */
set_error_handler ('_errorHandler');
set_exception_handler ('_exceptionHandler');
register_shutdown_function ('_shutdownHandler');
