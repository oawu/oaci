<?php defined ('BASEPATH') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2017, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

const CI_VERSION = '3.1.6';

include_once BASEPATH . 'core' . DIRECTORY_SEPARATOR . 'Load.php';

Load::sysCore ('Common.php'    , true);
Load::sysCore ('Charset.php'   , true, "Charset::init ();");
Load::sysCore ('Benchmark.php' , true, "Benchmark::init ();");
Load::sysCore ('Log.php'       , true);
Load::sysCore ('Exceptions.php', true, "Exceptions::init ();");
Load::sysCore ('Utf8.php'      , true, "Utf8::init ();");
Load::sysCore ('URL.php'       , true, "URL::init ();");
Load::sysCore ('Router.php'    , true, "Router::init ();");
Load::sysCore ('Output.php'    , true, "Output::init ();");
Load::sysCore ('Security.php'  , true, "Security::init ();");
Load::sysCore ('Input.php'     , true, "Input::init ();");
Load::sysCore ('Controller.php', true);
Load::sysCore ('Model.php'     , true);
Load::sysCore ('View.php'      , true);

// 載入 Composer autoload
config ('other', 'composer_autoload') && Load::file (FCPATH . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php', true);

Benchmark::mark ('loading_time:_base_classes_end');

$class = Router::getClass ();
$method = Router::getMethod ();
$path = APPPATH . 'controller' . DIRECTORY_SEPARATOR . Router::getDirectory () . $class . EXT;

if (!($class && $method !== '_' && file_exists ($path) && Load::file ($path) && class_exists ($class) && method_exists ($class, $method) && is_callable (array ($class, $method)) && ($reflection = new ReflectionMethod ($class, $method)) && ($reflection->isPublic () && !$reflection->isConstructor ())))
	return Exceptions::show404 ();

$params = array_slice (URL::rsegments (), 2);

if (method_exists ($class, '_remap')) {
	$params = array ($method, $params);
	$method = Router::setMethod ('_remap');
}

/* ======================================================
 *  開始 */

Benchmark::mark ('controller_execution_time_( ' . $class . ' / ' . $method . ' )_start');
$output = call_user_func_array (array (new $class (), $method), $params);
Benchmark::mark ('controller_execution_time_( ' . $class . ' / ' . $method . ' )_end');

/*  結束
 * ====================================================== */

Output::display ($output);

echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
var_dump (Benchmark::elapsedTime ('total_execution_time_start', 'total_execution_time_end'), round (memory_get_usage () / 1024 / 1024, 4) . 'MB');
exit ();