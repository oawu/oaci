<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2015 OA Wu Design
 */

$model['recycle']['limit'] = 100;
$model['recycle']['origin_id'] = 'origin_id';

$model['uploader']['temp_directory'] = FCPATH . 'temp' . DIRECTORY_SEPARATOR;
$model['uploader']['temp_file_name'] = uniqid (rand () . '_');

// $model['uploader']['file_name']['separate_symbol'] = '_';
// $model['uploader']['file_name']['auto_add_format'] = true;

// $model['uploader']['instances']['directory'] = FCPATH . APPPATH . 'third_party' . DIRECTORY_SEPARATOR . 'orm_image_uploaders' . DIRECTORY_SEPARATOR;
// $model['uploader']['instances']['class_suffix'] = 'Uploader';

// $model['uploader']['default_version'] = array ('' => array ());
// $model['uploader']['bucket']['type'] = 'local';
// $model['uploader']['bucket']['local']['base_directory'] = 'upload' . DIRECTORY_SEPARATOR;
