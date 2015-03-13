<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2015 OA Wu Design
 */

$orm_uploader['uploader']['debug'] = false;
$orm_uploader['uploader']['bucket'] = 'local'; // local、s3
$orm_uploader['uploader']['base_directory']['local'] = array ('upload');

$orm_uploader['uploader']['instance']['class_suffix'] = 'Uploader';
$orm_uploader['uploader']['instance']['directory'] = array ('application', 'third_party', 'orm_image_uploaders');

$orm_uploader['uploader']['unique_column'] = 'id';
$orm_uploader['uploader']['d4_url'] = '';

$orm_uploader['uploader']['temp_directory'] = array ('temp');
// $orm_uploader['uploader']['temp_file_name'] = uniqid (rand () . '_');


$orm_uploader['image_uploader']['debug'] = false;
$orm_uploader['image_uploader']['separate_symbol'] = '_';
$orm_uploader['image_uploader']['auto_add_format'] = true;
$orm_uploader['image_uploader']['temp_directory'] = array ('temp');
$orm_uploader['image_uploader']['default_version'] = array ('' => array ());
$orm_uploader['image_uploader']['d4_url'] = 'data:image/png;base64,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';




// $orm_uploader['debug'] = false;



