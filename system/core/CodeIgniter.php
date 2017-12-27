<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

const CI_VERSION = '3.1.6';

function _r ($class, $path = null, $init = true) {
	if (!file_exists ($path = ($path === null ? BASEPATH . 'core' . DIRECTORY_SEPARATOR : $path) . $class . '.php'))
		exit ('GG');

	require_once ($path);

	if ($init && class_exists ($class) && is_callable (array ($class, 'init')))
		$class::init ();

	return true;
}

_r ('Benchmark');
_r ('Config');
_r ('Common');
_r ('Log');
_r ('Exceptions');

// 自動載入 Composer
if ((Config::get ('composer_autoload') === true) && file_exists ($path = FCPATH . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php'))
	require_once ($path);

// 定義 charset
ini_set ('default_charset', $charset = ($charset = Config::get ('general', 'charset')) === null ? 'UTF-8' : $charset);
if (extension_loaded ('mbstring')) { define ('MB_ENABLED', true); @ini_set ('mbstring.internal_encoding', $charset); mb_substitute_character ('none'); } else { define ('MB_ENABLED', false); }
if (extension_loaded ('iconv')) { define ('ICONV_ENABLED', true); @ini_set ('iconv.internal_encoding', $charset); } else { define ('ICONV_ENABLED', false); }
isPhp ('5.6') && ini_set ('php.internal_encoding', $charset);

// 載入相容性的函式
array_map (function ($name) { return _r ($name, BASEPATH . 'core' . DIRECTORY_SEPARATOR . 'compat' . DIRECTORY_SEPARATOR); }, array ('mbstring', 'hash', 'password', 'standard'));

_r ('Utf8');
_r ('URI');
_r ('Router');
_r ('Output');
_r ('Security');
_r ('Input');
_r ('Controller');
_r ('Model', null, false);

Benchmark::mark ('loading_time:_base_classes_end');

$class = Router::getClass ();
$method = Router::getMethod ();

if (!($class && $method !== '_' && file_exists (($path = APPPATH . 'controllers' . DIRECTORY_SEPARATOR . Router::getDirectory ()) . $class . '.php') && _r ($class, $path, false) && class_exists ($class, false) && method_exists ($class, $method) && is_callable (array ($class, $method)) && ($reflection = new ReflectionMethod ($class, $method)) && ($reflection->isPublic () && !$reflection->isConstructor ())))
	return Exceptions::show404 ();

$params = array_slice (URI::rsegments (), 2);

if (method_exists ($class, '_remap')) {
	$params = array ($method, $params);
	$method = Router::setMethod ('_remap');
}


/* ======================================================
 *  開始 */

Benchmark::mark ('controller_execution_time_( ' . $class . ' / ' . $method . ' )_start');
$CI = new $class();
call_user_func_array (array (&$CI, $method), $params);
Benchmark::mark ('controller_execution_time_( ' . $class . ' / ' . $method . ' )_end');

/*  結束
 * ====================================================== */

Output::display ();

// echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
// var_dump (Benchmark::elapsedTime ('total_execution_time_start', 'total_execution_time_end'), round (memory_get_usage () / 1024 / 1024, 2).'MB');
// exit ();
// echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
// var_dump (Router::getDirectory ());
// var_dump (Router::getClass ());
// var_dump (Router::getMethod ());
// var_dump ();
// exit ();
