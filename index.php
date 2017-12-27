<?php

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2017, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

date_default_timezone_set ('Asia/Taipei');

file_exists ('_env.php') || exit ('尚未初始化！');

include '_env.php';

defined ('ENVIRONMENT') || define ('ENVIRONMENT', isset ($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'development');

switch (ENVIRONMENT) {
  case 'development':
    error_reporting (-1);
    ini_set ('display_errors', 1);
  break;

  case 'production':
    ini_set ('display_errors', 0);
    
    if (version_compare (PHP_VERSION, '5.3', '>='))
      error_reporting (E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
    else
      error_reporting (E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE);
  break;

  default:
    header ('HTTP/1.1 503 Service Unavailable.', true, 503);
    echo 'The application environment is not set correctly.';
    exit(1); // EXIT_ERROR
}

$system_path = 'sys';
$app_folder = 'app';
$view_folder = '';

if (defined ('STDIN')) chdir (dirname (__FILE__));

if (!is_dir ($system_path = ($_temp = realpath ($system_path)) !== false ? $_temp . DIRECTORY_SEPARATOR : (strtr (rtrim ($system_path, '/\\'), '/\\', DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR))) {
  header ('HTTP/1.1 503 Service Unavailable.', true, 503);
  echo 'Your system folder path does not appear to be set correctly. Please open the following file and correct this: ' . pathinfo (__FILE__, PATHINFO_BASENAME);
  exit (3);
}

define ('EXT', '.php');
define ('SELF', pathinfo (__FILE__, PATHINFO_BASENAME));
define ('BASEPATH', $system_path);
define ('FCPATH', dirname (__FILE__) . DIRECTORY_SEPARATOR);
define ('SYSDIR', basename (BASEPATH));

if (null === ($app_folder = is_dir ($app_folder) ? ($_temp = realpath ($app_folder)) !== false ? $_temp : strtr (rtrim ($app_folder, '/\\'), '/\\', DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR) : (is_dir(BASEPATH.$app_folder.DIRECTORY_SEPARATOR) ? BASEPATH . strtr (trim ($app_folder, '/\\'), '/\\', DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR) : null))) {
  header ('HTTP/1.1 503 Service Unavailable.', true, 503);
  echo 'Your application folder path does not appear to be set correctly. Please open the following file and correct this: ' . SELF;
  exit (3);
}

define ('APPPATH', $app_folder . DIRECTORY_SEPARATOR);

if (null === ($view_folder = !isset ($view_folder[0]) && is_dir (APPPATH . 'view' . DIRECTORY_SEPARATOR) ? APPPATH . 'view' : (is_dir ($view_folder) ? (($_temp = realpath($view_folder)) !== false) ? $_temp : strtr (rtrim ($view_folder, '/\\'), '/\\', DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR) : (is_dir (APPPATH . $view_folder . DIRECTORY_SEPARATOR) ? APPPATH . strtr (trim ($view_folder, '/\\'), '/\\', DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR) : null)))) {
  header ('HTTP/1.1 503 Service Unavailable.', true, 503);
  echo 'Your view folder path does not appear to be set correctly. Please open the following file and correct this: '.SELF;
  exit (3);
}

define('VIEWPATH', $view_folder . DIRECTORY_SEPARATOR);

require_once BASEPATH . 'core' . DIRECTORY_SEPARATOR . 'CodeIgniter.php';
