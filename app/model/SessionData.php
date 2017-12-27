<?php defined ('BASEPATH') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2017, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class SessionData extends Model {
  static $table_name = 'session_datas';

  public function destroy () {
    if (!isset ($this->id)) return false;

    return $this->delete ();
  }
}