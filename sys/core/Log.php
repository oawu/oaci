<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2017, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class Log {
  private static $config = array (
    'path' => FCPATH . 'log' . DIRECTORY_SEPARATOR,
    'extension' => '.log',
    'permissions' => 0777,
    'dateFormat' => 'Y-m-d H:i:s'
  );

  public static function info ($msg) {
    self::message (cc ('紀錄', 'g'), $msg);
  }
  public static function warning ($msg) {
    self::message (cc ('警告', 'y'), $msg);
  }
  public static function error ($msg) {
    self::message (cc ('錯誤', 'r'), $msg);
  }

  public static function msg ($title, $msg) {
    return call_user_func_array (array ('Log', 'message'), array (func_get_args ()));
  }
  public static function message ($title, $msg) {
    if (!(is_dir (self::$config['path']) && is_really_writable (self::$config['path'])))
      return false;

    $newfile = !file_exists ($path = self::$config['path'] . 'log-' . date ('Y-m-d') . self::$config['extension']);

    if (!$fp = @fopen ($path, FOPEN_WRITE_CREATE))
      return false;

    flock ($fp, LOCK_EX);

    $msg = self::formatLine (date (self::$config['dateFormat']), $title, $msg);

    for ($written = 0, $length = Charset::strlen ($msg); $written < $length; $written += $result)
      if (($result = fwrite ($fp, Charset::substr ($msg, $written))) === false)
        break;

    flock ($fp, LOCK_UN);
    fclose ($fp);

    $newfile &&
      chmod ($path, self::$config['permissions']);

    return is_int ($result);
  }

  private static function formatLine ($date, $title, $msg) {
    return cc ($date, 'W') . cc ('：', 'N') . $title . cc ('：', 'N') . $msg . "\n";
  }
}