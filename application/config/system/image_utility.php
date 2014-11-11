<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2014 OA Wu Design
 */

// gd、imgk
$image_utility['module'] = 'gd';
$image_utility['modules'] = array ('gd'   => 'ImageGdUtility',
                                   'imgk' => 'ImageImagickUtility');

$image_utility['gd']['allow_formats'] = array ('gif', 'jpg', 'png');
$image_utility['gd']['mime_formats'] = array ( 'image/gif'   => 'gif',
                                                'image/jpeg'  => 'jpg',
                                                'image/pjpeg' => 'jpg',
                                                'image/png'   => 'png',
                                                'image/x-png' => 'png');

$image_utility['imgk']['allow_formats'] = array ('gif', 'jpg', 'png');
$image_utility['imgk']['mime_formats'] = array ( 'image/gif'   => 'gif',
                                                  'image/jpeg'  => 'jpg',
                                                  'image/pjpeg' => 'jpg',
                                                  'image/png'   => 'png',
                                                  'image/x-png' => 'png');
