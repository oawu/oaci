<?php defined ('BASEPATH') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2017, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

if (!function_exists ('config')) {
  function config () {
    static $files, $keys;

    if (!(($args = func_get_args ()) && ($fileName = array_shift ($args))))
      exit ('找不到該 Config 檔案：' . $fileName);

    if (isset ($keys[$key = $fileName . implode ('_', $args)]))
      return $keys[$key];

    isset ($files[$fileName]) || $files[$fileName] = file_exists ($path = APPPATH . 'config' . DIRECTORY_SEPARATOR . ENVIRONMENT . DIRECTORY_SEPARATOR . $fileName . EXT) || file_exists ($path = APPPATH . 'config' . DIRECTORY_SEPARATOR . $fileName . EXT) ? include_once ($path) : null;

    if ($files[$fileName] === null && !($keys[$key] = null))
      exit ('找不到該 Config 檔案：' . $fileName);

    $t = $files[$fileName];

    foreach ($args as $arg)
      if (!$t = isset ($t[$arg]) ? $t[$arg] : null)
        break;

    return $keys[$key] = $t;
  }
}

if (!function_exists ('is_php')) {
	function is_php ($version) {
		static $_isPHP;
		return !isset ($_isPHP[$version = (string)$version]) ? $_isPHP[$version] = version_compare (PHP_VERSION, $version, '>=') : $_isPHP[$version];
	}
}

if (!function_exists ('remove_invisible_characters')) {
	function remove_invisible_characters ($str, $urlEncoded = true) {
		$n = array ();

		$urlEncoded && array_push ($n, '/%0[0-8bcef]/i', '/%1[0-9a-f]/i', '/%7f/i');

		array_push ($n, '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S');

		do {
			$str = preg_replace ($n, '', $str, -1, $count);
		} while ($count);

		return $str;
	}
}

if (!function_exists ('html_escape')) {
	function html_escape ($var, $doubleEncode = true) {
		if (!$var)
			return $var;

		if (!is_array ($var))
			return htmlspecialchars ($var, ENT_QUOTES, config ('other', 'charset'), $doubleEncode);

		foreach (array_keys ($var) as $key)
			$var[$key] = html_escape ($var[$key], $doubleEncode);

		return $var;
	}
}

if (!function_exists ('stringify_attributes')) {
	function stringify_attributes ($attrs, $js = false) {
		$atts = '';

		if (!$attrs)
			return $atts;
		
		if (is_string ($attrs))
			return ' ' . $attrs;
		
		if (!is_array ($attrs))
			return $atts;

		foreach ($attrs as $key => $val)
			$atts .= $js ? $key . '=' . $val . ',' : ' ' . $key . '="' . $val . '"';

		return rtrim ($atts, ',');
	}
}

if (!function_exists ('is_really_writable')) {
  function is_really_writable ($file) {
    if (DIRECTORY_SEPARATOR === '/' && (is_php ('5.4') || !ini_get ('safe_mode')))
      return is_writable ($file);

    if (is_dir ($file)) {
      if (($fp = @fopen ($file = rtrim ($file, '/') . '/' . md5 (mt_rand ()), 'ab')) === false)
        return false;
 
      fclose ($fp);
      @chmod ($file, 0777);
      @unlink ($file);

      return true;
    }

    if (!is_file ($file) || ($fp = @fopen ($file, 'ab')) === false)
      return false;
 
    fclose ($fp);
    return true;
  }
}

if (!function_exists ('request_is_cli')) {
	function request_is_cli () {
		return PHP_SAPI === 'cli' || defined ('STDIN');
	}
}

if (!function_exists ('request_is_https')) {
	function request_is_https () {
		return (!empty ($_SERVER['HTTPS']) && strtolower ($_SERVER['HTTPS']) !== 'off')
				|| (isset ($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower ($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https')
				|| (!empty ($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower ($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off');
	}
}

if (!function_exists ('request_is_method')) {
	function request_is_method () {
		return strtolower (request_is_cli ()
					 ? 'cli'
					 : (isset ($_SERVER['REQUEST_METHOD'])
					 	 ? $_SERVER['REQUEST_METHOD']
					 	 : (isset ($_POST['_method'])
					 	 	 ? $_POST['_method']
					 	 	 : 'get')));
	}
}

if (!function_exists ('request_is_ajax')) {
	function request_is_ajax () {
    return isset ($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower ($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
  }
}

if (!function_exists ('array_2d_to_1d')) {
  function array_2d_to_1d ($array) {
    $messages = array ();
    foreach ($array as $key => $value)
      if (is_array ($value)) $messages = array_merge ($messages, $value);
      else array_push ($messages, $value);
    return $messages;
  }
}

if (!function_exists ('cc')) {
  function cc ($str, $fc = null, $bc = null) {
    if (!strlen ($str)) return "";
    // if (!CLI) return $str;

    $nstr = "";
    $keys = array ('n' => '30', 'w' => '37', 'b' => '34', 'g' => '32', 'c' => '36', 'r' => '31', 'p' => '35', 'y' => '33');
    if ($fc && in_array (strtolower ($fc), array_map ('strtolower', array_keys ($keys)))) {
      $fc = !in_array (ord ($fc[0]), array_map ('ord', array_keys ($keys))) ? in_array (ord ($fc[0]) | 0x20, array_map ('ord', array_keys ($keys))) ? '1;' . $keys[strtolower ($fc[0])] : null : $keys[$fc[0]];
      $nstr .= $fc ? "\033[" . $fc . "m" : "";
    }
    $nstr .= $bc && in_array (strtolower ($bc), array_map ('strtolower', array_keys ($keys))) ? "\033[" . ($keys[strtolower ($bc[0])] + 10) . "m" : "";

    if (substr ($str, -1) == "\n") { $str = substr ($str, 0, -1); $has_new_line = true; } else { $has_new_line = false; }
    $nstr .=  $str . "\033[0m";
    $nstr = $nstr . ($has_new_line ? "\n" : "");

    return $nstr;
  }
}