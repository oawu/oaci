<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

class Log {
  private static $funcOverload;
  
  public static function init () {
    isset (self::$funcOverload) || self::$funcOverload = (extension_loaded ('mbstring') && ini_get ('mbstring.func_overload'));
  }
  public static function isReallyWritable ($file) {
    if (DIRECTORY_SEPARATOR === '/' && (isPHP ('5.4') || !ini_get ('safe_mode')))
      return is_writable ($file);

    if (is_dir ($file)) {
      if (($fp = @fopen ($file = rtrim ($file, '/') . '/' . md5 (mt_rand ()), 'ab')) === false)
        return false;
 
      fclose ($fp);
      @chmod ($file, 0777);
      @unlink ($file);

      return true;
    } else if (!is_file ($file) || ($fp = @fopen ($file, 'ab')) === false) {
      return false;
    }
 
    fclose ($fp);
    return true;
  }
  public static function message ($msg) {
    $config = Config::get ('log');

    if (!(is_dir ($config['path']) && self::isReallyWritable ($config['path'])))
      return false;

    $newfile = !file_exists ($config['path'] = $config['path'] . 'log-' . date ('Y-m-d') . $config['extension']);

    if (!$fp = @fopen ($config['path'], FOPEN_WRITE_CREATE))
      return false;

    flock ($fp, LOCK_EX);

    $msg = self::formatLine (date ($config['dateFormat']), $msg);

    for ($written = 0, $length = self::strlen ($msg); $written < $length; $written += $result)
      if (($result = fwrite ($fp, self::substr ($msg, $written))) === false)
        break;

    flock ($fp, LOCK_UN);
    fclose ($fp);

    if ($newfile) chmod ($config['path'], $config['permissions']);

    return is_int ($result);
  }

  private static function formatLine ($date, $msg) {
    return $date . ' --> ' . $msg . "\n";
  }

  private static function strlen ($str) {
    return self::$funcOverload ? mb_strlen ($str, '8bit') : strlen ($str);
  }

  private static function substr ($str, $start, $length = NULL) {
    if (self::$funcOverload) {
      isset($length) || $length = ($start >= 0 ? self::strlen ($str) - $start : -$start);
      return mb_substr ($str, $start, $length, '8bit');
    }

    return isset ($length) ? substr ($str, $start, $length) : substr ($str, $start);
  }
}