<?php defined ('BASEPATH') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2017, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class Log {
  public static function logMessage ($msg) {
    self::message (cc ('紀錄', 'g'), $msg);
  }
  public static function warningMessage ($msg) {
    self::message (cc ('警告', 'y'), $msg);
  }
  public static function errorMessage ($msg) {
    self::message (cc ('錯誤', 'r'), $msg);
  }
  public static function message ($title, $msg) {
    $config = config ('log');

    if (!(is_dir ($config['path']) && is_really_writable ($config['path'])))
      return false;

    $newfile = !file_exists ($config['path'] = $config['path'] . 'log-' . date ('Y-m-d') . $config['extension']);

    if (!$fp = @fopen ($config['path'], FOPEN_WRITE_CREATE))
      return false;

    flock ($fp, LOCK_EX);

    $msg = self::formatLine (date ($config['dateFormat']), $title, $msg);

    for ($written = 0, $length = Charset::strlen ($msg); $written < $length; $written += $result)
      if (($result = fwrite ($fp, Charset::substr ($msg, $written))) === false)
        break;

    flock ($fp, LOCK_UN);
    fclose ($fp);

    if ($newfile) chmod ($config['path'], $config['permissions']);

    return is_int ($result);
  }

  private static function formatLine ($date, $title, $msg) {
    return cc ($date, 'W') . cc ('：', 'N') . $title . cc ('：', 'N') . $msg . "\n";
  }
}