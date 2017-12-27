<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2017, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

Load::sysCore ('Common.php', true, "config ('defines');");
Load::sysCore ('Charset.php', true, "Charset::init ();");
Load::sysCore ('Benchmark.php', true, "Benchmark::init ();");
Load::sysCore ('Log.php', true);
Load::sysCore ('Utf8.php', true, "Utf8::init ();");
Load::sysCore ('URL.php', true, "URL::init ();");
Load::sysCore ('Router.php', true, "Router::init ();");
Load::sysCore ('Output.php', true, "Output::init ();");
Load::sysCore ('Security.php', true, "Security::init ();");
Load::sysCore ('Input.php', true, "Input::init ();");
Load::sysCore ('Controller.php', true);
Load::sysCore ('Model.php', true);
Load::sysCore ('View.php', true);
config ('other', 'composer_autoload') && Load::file (FCPATH . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php', true);

Benchmark::markEnd ('核心');

$class  = Router::getClass ();
$method = Router::getMethod ();
$path   = APPPATH . 'controller' . DIRECTORY_SEPARATOR . Router::getDirectory () . $class . EXT;

if (!($class && $method !== '_' && file_exists ($path) && Load::file ($path) && class_exists ($class) && method_exists ($class, $method) && is_callable (array ($class, $method)) && ($reflection = new ReflectionMethod ($class, $method)) && ($reflection->isPublic () && !$reflection->isConstructor ())))
  return show404 ();

$params = array_slice (URL::rsegments (), 2);

if (method_exists ($class, '_remap')) {
  $params = array ($method, $params);
  $method = Router::setMethod ('_remap');
}

/* ======================================================
 *  開始 Controller */

Benchmark::markStar ('Controller ( ' . $class . ' / ' . $method . ' )');
$output = call_user_func_array (array (new $class (), $method), $params);
Benchmark::markEnd ('Controller ( ' . $class . ' / ' . $method . ' )');

/*  結束 Controller
 * ====================================================== */

Output::display ($output);
Benchmark::markEnd ('整體');
