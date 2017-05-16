<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

$model['recycle']['limit'] = 100;
$model['recycle']['origin_id'] = 'origin_id';

$model['cache']['is_enabled'] = false;
$model['cache']['driver'] = 'file'; // 'file', 'redis'
$model['cache']['file_path'] = Cfg::_system ('cache', 'model');
$model['cache']['redis_main_key'] = array ('model');
$model['cache']['d4_cache_time'] = 300; // sec
