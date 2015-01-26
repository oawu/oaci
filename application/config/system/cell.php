<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2015 OA Wu Design
 */

$cell['is_enabled'] = true;

$cell['cache_folder']      = APPPATH . 'cell' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR;
$cell['controller_folder'] = APPPATH . 'cell' . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR;
$cell['view_folder']       = APPPATH . 'cell' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR;

$cell['d4_time'] = 60;
$cell['class_suffix']  = '_cells';
$cell['method_prefix'] = '_cache_';
$cell['file_prefix']   = '_cell';