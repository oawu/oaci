<?php

define ('ENVIRONMENT', isset ($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'development');

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

$system_path = 'system';

$application_folder = 'application';

$view_folder = '';

if (defined ('STDIN')) chdir (dirname (__FILE__));

if (!is_dir ($system_path = ($_temp = realpath ($system_path)) !== false ? $_temp . DIRECTORY_SEPARATOR : (strtr (rtrim ($system_path, '/\\'), '/\\', DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR))) {
	header ('HTTP/1.1 503 Service Unavailable.', true, 503); echo 'Your system folder path does not appear to be set correctly. Please open the following file and correct this: ' . pathinfo (__FILE__, PATHINFO_BASENAME); exit (3);
}

define('SELF', pathinfo (__FILE__, PATHINFO_BASENAME));
define('BASEPATH', $system_path);
define('FCPATH', dirname (__FILE__) . DIRECTORY_SEPARATOR);
define('SYSDIR', basename (BASEPATH));

if (null === ($application_folder = is_dir ($application_folder) ? ($_temp = realpath ($application_folder)) !== false ? $_temp : strtr (rtrim ($application_folder, '/\\'), '/\\', DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR) : (is_dir(BASEPATH.$application_folder.DIRECTORY_SEPARATOR) ? BASEPATH . strtr (trim ($application_folder, '/\\'), '/\\', DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR) : null))) {
	header ('HTTP/1.1 503 Service Unavailable.', true, 503); echo 'Your application folder path does not appear to be set correctly. Please open the following file and correct this: ' . SELF; exit (3);
}

define ('APPPATH', $application_folder . DIRECTORY_SEPARATOR);

if (null === ($view_folder = !isset ($view_folder[0]) && is_dir (APPPATH . 'views' . DIRECTORY_SEPARATOR) ? APPPATH . 'views' : (is_dir ($view_folder) ? (($_temp = realpath($view_folder)) !== false) ? $_temp : strtr (rtrim ($view_folder, '/\\'), '/\\', DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR) : (is_dir (APPPATH . $view_folder . DIRECTORY_SEPARATOR) ? APPPATH . strtr (trim ($view_folder, '/\\'), '/\\', DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR) : null)))) {
	header ('HTTP/1.1 503 Service Unavailable.', true, 503); echo 'Your view folder path does not appear to be set correctly. Please open the following file and correct this: '.SELF; exit (3);
}

define('VIEWPATH', $view_folder . DIRECTORY_SEPARATOR);

require_once BASEPATH . 'core' . DIRECTORY_SEPARATOR . 'CodeIgniter.php';
