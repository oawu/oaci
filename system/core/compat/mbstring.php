<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

if (MB_ENABLED === true)
	return;

if (!function_exists ('mb_strlen')) {
	function mb_strlen ($str, $encoding = null) {
		if (ICONV_ENABLED === true)
			return iconv_strlen ($str, isset ($encoding) ? $encoding : (($t = Config::get ('general', 'charset')) ? $t : 'UTF-8'));
		return strlen ($str);
	}
}

if (!function_exists ('mb_strpos')) {
	function mb_strpos ($haystack, $needle, $offset = 0, $encoding = null) {
		if (ICONV_ENABLED === true)
			return iconv_strpos ($haystack, $needle, $offset, isset ($encoding) ? $encoding : Config::get ('general', 'charset'));
		return strpos ($haystack, $needle, $offset);
	}
}

if (!function_exists ('mb_substr')) {
	function mb_substr ($str, $start, $length = null, $encoding = null) {
		if (ICONV_ENABLED === true) {
			isset ($encoding) || $encoding = (($t = Config::get ('general', 'charset')) ? $t : 'UTF-8');
			return iconv_substr ($str, $start, isset($length) ? $length : iconv_strlen($str, $encoding), $encoding);
		}

		return isset ($length) ? substr ($str, $start, $length) : substr ($str, $start);
	}
}
