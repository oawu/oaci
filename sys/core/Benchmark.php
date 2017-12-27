<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2017, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class Benchmark {
  private static $marker = array ();

  public static function init () {
    Benchmark::mark ('total_execution_time_start');
    Benchmark::mark ('loading_time:_base_classes_start');
  }

  public static function mark ($name) {
    self::$marker[$name] = microtime (true);
  }

  public static function elapsedTime ($point1 = '', $point2 = '', $decimals = 4) {
    if ($point1 === '')
      return '{elapsed_time}';

    if (!isset (self::$marker[$point1]))
      return '';

    if (!isset (self::$marker[$point2]))
      self::$marker[$point2] = microtime (true);

    return number_format (self::$marker[$point2] - self::$marker[$point1], $decimals);
  }

  public static function memory_usage () {
    return '{memory_usage}';
  }
}
