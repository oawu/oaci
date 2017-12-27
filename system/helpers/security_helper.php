<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

if (!function_exists ('sanitizeFilename')) {
	function sanitizeFilename ($filename) {
		return Security::sanitizeFilename ($filename);
	}
}

if (!function_exists ('stripImageTags')) {
	function stripImageTags ($str) {
		return Security::stripImageTags($str);
	}
}















if (!function_exists ('xss_clean'))
{
	function xss_clean($str, $is_image = FALSE)
	{
		return get_instance()->security->xss_clean($str, $is_image);
	}
}

// ------------------------------------------------------------------------


// --------------------------------------------------------------------

if (!function_exists ('do_hash'))
{
	/**
	 * Hash encode a string
	 *
	 * @todo	Remove in version 3.1+.
	 * @deprecated	3.0.0	Use PHP's native hash() instead.
	 * @param	string	$str
	 * @param	string	$type = 'sha1'
	 * @return	string
	 */
	function do_hash($str, $type = 'sha1')
	{
		if ( ! in_array(strtolower($type), hash_algos()))
		{
			$type = 'md5';
		}

		return hash($type, $str);
	}
}

// ------------------------------------------------------------------------


// ------------------------------------------------------------------------

if (!function_exists ('encode_php_tags'))
{
	/**
	 * Convert PHP tags to entities
	 *
	 * @param	string
	 * @return	string
	 */
	function encode_php_tags($str)
	{
		return str_replace(array('<?', '?>'), array('&lt;?', '?&gt;'), $str);
	}
}
