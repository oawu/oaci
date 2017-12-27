<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2017, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

if (!function_exists ('use_model')) {
  function use_model () {
    static $used;

    if (!empty ($used))
      return true;

    if (!$database = config ('database'))
      return false;

    Load::file (BASEPATH . 'model' . DIRECTORY_SEPARATOR . 'ActiveRecord.php', true);

    ActiveRecord\Config::initialize (function ($cfg) use ($database) {
      $cfg->set_model_directory (APPPATH . 'model');
      $cfg->set_connections (array_combine (array_keys ($database['groups']), array_map (function ($group) { return $group['dbdriver'] . '://' . $group['username'] . ':' . $group['password'] . '@' . $group['hostname'] . '/' . $group['database'] . '?charset=' . $group['char_set']; }, $database['groups'])), $database['active_group']);

      class_exists ('Log') && $cfg->setLog ('Log');
    });

    class_alias ('ActiveRecord\Connection', 'ModelConnection');
    class Model extends ActiveRecord\Model {}

    Load::sysLib ('Uploader.php');

    return $used = true;
  }

  if (!function_exists ('create_model')) {
    function create_model ($modelName, $arr) {
      return ($obj = $modelName::create (array_intersect_key ($arr, $modelName::table ()->columns))) && $obj->is_valid () ? $obj : null;
    }
  }
}

config ('model', 'auto_load') && use_model ();