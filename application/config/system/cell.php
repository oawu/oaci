<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2015 OA Wu Design
 */


$cell['cache_folder']      = APPPATH . 'cell' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR;
$cell['controller_folder'] = APPPATH . 'cell' . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR;
$cell['view_folder']       = APPPATH . 'cell' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR;

$cell['is_enabled'] = true;
$cell['driver'] = 'file'; // 'file', 'redis'
$cell['redis_main_key'] = 'cell';
$cell['file_prefix']   = '_cell';
$cell['file_is_md5'] = true;
$cell['d4_cache_time'] = 60;

$cell['folders'] = array (
    'cache'      => array ('cell', 'cache'),
    'controller' => array ('cell', 'controllers'),
    'view'       => array ('cell', 'views')
  );

$cell['class_suffix']  = '_cell';
$cell['method_prefix'] = '_cache_';
//*** command 也要改
