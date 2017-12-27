<?php defined ('BASEPATH') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2017, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

if (!function_exists ('force_download')) {
	function force_download ($filename = '', $data = '', $set_mime = false) {
		if ($filename === '' || $data === '')
			return;

		if ($data === null) {
			if (!@is_file ($filename) || ($filesize = @filesize ($filename)) === false)
				return;

			$filepath = $filename;
			$filename = explode ('/', str_replace (DIRECTORY_SEPARATOR, '/', $filename));
			$filename = end ($filename);
		} else {
			$filesize = strlen ($data);
		}

		$mime = 'application/octet-stream';

		$x = explode ('.', $filename);
		$extension = end ($x);

		if ($set_mime === true) {
			if (count ($x) === 1 || $extension === '')
				return;

			if ($t = config ('mimes', $extension))
				$mime = is_array ($t) ? $t[0] : $t;
		}

		if (count ($x) !== 1 && isset ($_SERVER['HTTP_USER_AGENT']) && preg_match ('/Android\s(1|2\.[01])/', $_SERVER['HTTP_USER_AGENT'])) {
			$x[count($x) - 1] = strtoupper ($extension);
			$filename = implode ('.', $x);
		}

		if ($data === null && ($fp = @fopen ($filepath, 'rb')) === false)
			return;

		if (ob_get_level () !== 0 && @ob_end_clean () === false)
			@ob_clean ();

		header ('Content-Type: ' . $mime);
		header ('Content-Disposition: attachment; filename="' . $filename . '"');
		header ('Expires: 0');
		header ('Content-Transfer-Encoding: binary');
		header ('Content-Length: ' . $filesize);
		header ('Cache-Control: private, no-transform, no-store, must-revalidate');

		if ($data !== null)
			exit($data);

		while (!feof ($fp) && ($data = fread ($fp, 1048576)) !== false)
			echo $data;

		fclose ($fp);
		exit;
	}
}
