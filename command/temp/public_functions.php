<?php

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2015 OA Wu Design
 */

if (!function_exists ('read_file')) {
  function read_file ($file) {
    if (!file_exists ($file)) return false;

    if (function_exists ('file_get_contents')) return file_get_contents ($file);

    if (!$fp = @fopen ($file, FOPEN_READ)) return false;

    flock ($fp, LOCK_SH);

    $data = '';
    if (filesize($file) > 0)
      $data =& fread ($fp, filesize ($file));

    flock ($fp, LOCK_UN);
    fclose ($fp);

    return $data;
  }
}

if (!function_exists ('write_file')) {
  function write_file ($path, $data, $mode = 'wb') {
    if (!$fp = @fopen($path, $mode))
      return false;

    flock ($fp, LOCK_EX);
    fwrite ($fp, $data);
    flock ($fp, LOCK_UN);
    fclose ($fp);
    return true;
  }
}

if (!function_exists ('delete_files')) {
  function delete_files ($path, $del_dir = false, $level = 0) {
    $path = rtrim ($path, DIRECTORY_SEPARATOR);

    if (!$current_dir = @opendir ($path)) return false;

    while (false !== ($filename = @readdir ($current_dir)))
      if (($filename != ".") && ($filename != "..")) {
        if (is_dir ($path . DIRECTORY_SEPARATOR . $filename)) {
          if (substr ($filename, 0, 1) != '.')
            delete_files ($path . DIRECTORY_SEPARATOR . $filename, $del_dir, $level + 1);
        } else {
          unlink ($path . DIRECTORY_SEPARATOR . $filename);
        }
      }

    @closedir ($current_dir);

    if (($del_dir == true) && ($level > 0)) return @rmdir ($path);

    return true;
  }
}

if (!function_exists ('load_view')) {
  function load_view ($_oa_path = '', $data = array ()) {
    if (!$_oa_path) return '';

    extract ($data);
    global $_navbar_mobile, $_footer, $_list_more, $_mobile_right_slides, $_nav_items, $_pins, $_tags, $_list, $_title, $_url, $_author, $_keywords, $_description, $_og;
    ob_start ();

    if (((bool)@ini_get ('short_open_tag') === FALSE) && (false == TRUE)) echo eval ('?>'.preg_replace ("/;*\s*\?>/", "; ?>", str_replace ('<?=', '<?php echo ', file_get_contents ($_oa_path))));
    else include $_oa_path;

    $buffer = ob_get_contents ();
    @ob_end_clean ();

    return $buffer;
  }
}