<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

if ($hidden_list) foreach ($hidden_list as $hidden) echo oa_hidden ($hidden) . (ENVIRONMENT !== 'production' ? "\n" : '');