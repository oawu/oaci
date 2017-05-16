<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

if ($meta_list) foreach ($meta_list as $meta) echo oa_meta ($meta) . (ENVIRONMENT !== 'production' ? "\n" : '');
