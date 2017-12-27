<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

if (!function_exists ('isPHP')) {
	function isPHP ($version) {
		static $_isPHP;
		$version = (string) $version;
		return !isset ($_isPHP[$version]) ? $_isPHP[$version] = version_compare (PHP_VERSION, $version, '>=') : $_isPHP[$version];
	}
}

if (!function_exists('isCli')) {
	function isCli () {
		return (PHP_SAPI === 'cli') || defined ('STDIN');
	}
}

if (!function_exists ('removeInvisibleCharacters')) {
	function removeInvisibleCharacters ($str, $urlEncoded = true) {
		$n = array ();

		// url encoded 00-08, 11, 12, 14, 15
		// url encoded 16-31
		// url encoded 127
		if ($urlEncoded) array_push ($n, '/%0[0-8bcef]/i', '/%1[0-9a-f]/i', '/%7f/i');

		// 00-08, 11, 12, 14-31, 127
		array_push ($n, '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S');

		do {
			$str = preg_replace ($n, '', $str, -1, $count);
		} while ($count);

		return $str;
	}
}

if (!function_exists ('htmlEscape')) {
	function htmlEscape ($var, $doubleEncode = true) {
		if (empty ($var)) return $var;

		if (is_array ($var)) {
			foreach (array_keys ($var) as $key)
				$var[$key] = htmlEscape ($var[$key], $doubleEncode);

			return $var;
		}

		return htmlspecialchars ($var, ENT_QUOTES, ($t = Config::get ('general', 'charset')) ? $t : 'UTF-8', $doubleEncode);
	}
}

if (!function_exists ('stringifyAttributes')) {
	function stringifyAttributes ($attributes, $js = false) {
		$atts = '';

		if (!$attributes) return $atts;
		if (is_string ($attributes)) return ' ' . $attributes;
		if (!is_array ($attributes)) return $atts;

		foreach ($attributes as $key => $val)
			$atts .= ($js) ? $key . '=' . $val . ',' : ' ' . $key . '="' . $val . '"';

		return rtrim ($atts, ',');
	}
}

if (!function_exists ('selfIsHttps')) {
	function selfIsHttps () {
		return (!empty ($_SERVER['HTTPS']) && strtolower ($_SERVER['HTTPS']) !== 'off') || (isset ($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower ($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') || (!empty ($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower ($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off');
	}
}

if (!function_exists ('getServerMethodRequest')) {
	function getServerMethodRequest () {
		return isCli () ? 'cli' : (isset ($_SERVER['REQUEST_METHOD']) ? strtolower ($_SERVER['REQUEST_METHOD']) : (isset ($_POST['_method']) ? strtolower ($_POST['_method']) : 'get'));
	}
}

if (!function_exists ('isAjaxRequest')) {
	function isAjaxRequest () {
    return !empty ($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower ($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
  }
}

// // if (false) {
// // 	function &load_class_1 ($class, $directory = 'libraries', $param = null) {
// // 		static $_classes = array ();

// // 		if (isset($_classes[$class]))
// // 			return $_classes[$class];

// // 		$name = null;

// // 		foreach (array (APPPATH, BASEPATH) as $path)
// // 			if (file_exists ($p = $path . $directory . DIRECTORY_SEPARATOR . $class . '.php')) {
// // 				require_once ($p);
// // 				$name = !class_exists ('CI_' . $class) ? $class : $name;
// // 				break;
// // 			}

// // 		if (file_exists (APPPATH . $directory . DIRECTORY_SEPARATOR . config_item('subclass_prefix').$class.'.php'))
// // 		{
// // 			$name = config_item('subclass_prefix').$class;

// // 			if (class_exists($name, false) === false)
// // 			{
// // 				require_once(APPPATH.$directory.'/'.$name.'.php');
// // 			}
// // 		}

// // 		// Did we find the class?
// // 		if ($name === false)
// // 		{
// // 			// Note: We use exit() rather than show_error() in order to avoid a
// // 			// self-referencing loop with the Exceptions class
// // 			set_status_header(503);
// // 			echo 'Unable to locate the specified class: '.$class.'.php';
// // 			exit(5); // EXIT_UNK_CLASS
// // 		}

// // 		// Keep track of what we just loaded
// // 		is_loaded($class);

// // 		$_classes[$class] = isset($param)
// // 			? new $name($param)
// // 			: new $name();
// // 		return $_classes[$class];
// // 	}
// // }

// // // --------------------------------------------------------------------

// // if ( ! function_exists('is_loaded'))
// // {
// // 	/**
// // 	 * Keeps track of which libraries have been loaded. This function is
// // 	 * called by the load_class() function above
// // 	 *
// // 	 * @param	string
// // 	 * @return	array
// // 	 */
// // 	function &is_loaded($class = '')
// // 	{
// // 		static $_is_loaded = array();

// // 		if ($class !== '')
// // 		{
// // 			$_is_loaded[strtolower($class)] = $class;
// // 		}

// // 		return $_is_loaded;
// // 	}
// // }


// // // ------------------------------------------------------------------------

// // if (false)
// // {
// // 	/**
// // 	 * Loads the main config.php file
// // 	 *
// // 	 * This function lets us grab the config file even if the Config class
// // 	 * hasn't been instantiated yet
// // 	 *
// // 	 * @param	array
// // 	 * @return	array
// // 	 */
// // 	function &get_config(Array $replace = array())
// // 	{
// // 		static $config;

// // 		if (empty($config)) {
			
// // 			if (file_exists (APPPATH.'config' . DIRECTORY_SEPARATOR . 'general.php'))
// // 				$config = include (APPPATH.'config' . DIRECTORY_SEPARATOR . 'general.php');
// // 			else if (file_exists(APPPATH.'config' . DIRECTORY_SEPARATOR . ENVIRONMENT.DIRECTORY_SEPARATOR.'general.php'))
// // 				$config = include (APPPATH.'config' . DIRECTORY_SEPARATOR . ENVIRONMENT.DIRECTORY_SEPARATOR.'general.php');
// // 			else 
// // 				$config = null;


// // 			if ($config === null) {
// // 				set_status_header (503);
// // 				echo 'The configuration file does not exist.';
// // 				exit (3); // EXIT_CONFIG
// // 			}

// // 			if (!is_array ($config)) {
// // 				set_status_header (503);
// // 				echo 'Your config file does not appear to be formatted correctly.';
// // 				exit (3); // EXIT_CONFIG
// // 			}
// // 		}

// // 		foreach ($replace as $key => $val)
// // 			$config[$key] = $val;

// // 		return $config;
// // 	}
// // }

// // 

// // ------------------------------------------------------------------------

// // if (false)
// // {
// // 	/**
// // 	 * Returns the specified config item
// // 	 *
// // 	 * @param	string
// // 	 * @return	mixed
// // 	 */
// // 	function config_item($item)
// // 	{
// // 		static $_config;

// // 		if (empty($_config))
// // 		{
// // 			// references cannot be directly assigned to static variables, so we use an array
// // 			$_config[0] =& get_config();
// // 		}

// // 		return isset($_config[0][$item]) ? $_config[0][$item] : null;
// // 	}
// // }







// // ------------------------------------------------------------------------

// // if ( ! function_exists('get_mimes'))
// // {
// // 	/**
// // 	 * Returns the MIME types array from config/mimes.php
// // 	 *
// // 	 * @return	array
// // 	 */
// // 	function &get_mimes()
// // 	{
// // 		static $_mimes;

// // 		if (empty($_mimes))
// // 		{
// // 			$_mimes = file_exists(APPPATH.'config/mimes.php')
// // 				? include(APPPATH.'config/mimes.php')
// // 				: array();

// // 			if (file_exists(APPPATH.'config/'.ENVIRONMENT.'/mimes.php'))
// // 			{
// // 				$_mimes = array_merge($_mimes, include(APPPATH.'config/'.ENVIRONMENT.'/mimes.php'));
// // 			}
// // 		}

// // 		return $_mimes;
// // 	}
// // }

