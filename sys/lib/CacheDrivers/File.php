<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2017, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class CacheFileDriver {
  private $path;

  public function __construct () {
    $config = config ('cache', 'drivers', 'file');
    $this->path = $config['path'] . $config['prefix'];

    if (!$this->isSupported ())
      gg ('[Cache] CacheFileDriver 錯誤，路徑無法寫入。');

    Load::sysFunc ('file.php');
  }

  public function get ($id) {
    if (($data = $this->_get ($id)) === null)
      return null;

    return is_array ($data) ? $data['data'] : $data;
  }

  private function _get ($id) {
    if (!is_file ($this->path . $id))
      return null;

    $data = unserialize (read_file ($this->path . $id));

    if (!($data['ttl'] > 0 && time () > $data['time'] + $data['ttl']))
      return $data;

    unlink ($this->path . $id);
    return null;
  }

  public function save ($id, $data, $ttl = 60) {
    $contents = array (
      'time' => time (),
      'ttl' => $ttl,
      'data' => $data
    );

    if (!write_file ($this->path . $id, serialize ($contents)))
      return false;

    chmod ($this->path . $id, 0640);
    return true;
  }

  public function delete ($id) {
    return is_file ($this->path . $id) ? unlink ($this->path . $id) : false;
  }

  public function clean () {
    return delete_files ($this->path, false, true);
  }

  public function info () {
    return get_dir_file_info ($this->path);
  }

  public function metadata ($id) {
    if (!is_file ($this->path . $id))
      return null;

    $data = unserialize (file_get_contents ($this->path . $id));

    if (!is_array ($data))
      return null;

    $mtime = filemtime ($this->path . $id);

    return !isset ($data['ttl'], $data['time']) ? false : array (
      'expire' => $data['time'] + $data['ttl'],
      'mtime'  => $mtime
    );
  }

  public function isSupported () {
    return is_really_writable ($this->path);
  }
}
