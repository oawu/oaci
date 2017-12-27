<?php defined ('BASEPATH') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2017, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

return array (
  'stati' => array (
    100 => 'Continue',                        101 => 'Switching Protocols',
    
    200 => 'OK',                              201 => 'Created',
    202 => 'Accepted',                        203 => 'Non-Authoritative Information',
    204 => 'No Content',                      205 => 'Reset Content',
    206 => 'Partial Content',
    
    300 => 'Multiple Choices',                301 => 'Moved Permanently',
    302 => 'Found',                           303 => 'See Other',
    304 => 'Not Modified',                    305 => 'Use Proxy',
    307 => 'Temporary Redirect',

    400 => 'Bad Request',                     401 => 'Unauthorized',
    402 => 'Payment Required',                403 => 'Forbidden',
    404 => 'Not Found',                       405 => 'Method Not Allowed',
    406 => 'Not Acceptable',                  407 => 'Proxy Authentication Required',
    408 => 'Request Timeout',                 409 => 'Conflict',
    410 => 'Gone',                            411 => 'Length Required',
    412 => 'Precondition Failed',             413 => 'Request Entity Too Large',
    414 => 'Request-URI Too Long',            415 => 'Unsupported Media Type',
    416 => 'Requested Range Not Satisfiable', 417 => 'Expectation Failed',
    422 => 'Unprocessable Entity',            426 => 'Upgrade Required',
    428 => 'Precondition Required',           429 => 'Too Many Requests',
    431 => 'Request Header Fields Too Large',

    500 => 'Internal Server Error',           501 => 'Not Implemented',
    502 => 'Bad Gateway',                     503 => 'Service Unavailable',
    504 => 'Gateway Timeout',                 505 => 'HTTP Version Not Supported',
    511 => 'Network Authentication Required'),
  'levels' => array (
    E_ERROR         => 'Error',         E_WARNING         => 'Warning',
    E_PARSE         => 'Parsing Error', E_NOTICE          => 'Notice',
    E_CORE_ERROR    => 'Core Error',    E_CORE_WARNING    => 'Core Warning',
    E_COMPILE_ERROR => 'Compile Error', E_COMPILE_WARNING => 'Compile Warning',
    E_USER_ERROR    => 'User Error',    E_USER_WARNING    => 'User Warning',
    E_USER_NOTICE   => 'User Notice',   E_STRICT          => 'Runtime Notice')
);