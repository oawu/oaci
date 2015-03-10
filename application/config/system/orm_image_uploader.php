<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2015 OA Wu Design
 */

$model['uploader']['instances']['directory'] = FCPATH . APPPATH . 'third_party' . DIRECTORY_SEPARATOR . 'orm_image_uploaders' . DIRECTORY_SEPARATOR;

$orm_image_uploader['instance']['class_suffix'] = 'Uploader';
$orm_image_uploader['instance']['directory'] = array ('application', 'third_party', 'orm_image_uploaders');

$orm_image_uploader['bucket'] = 'local'; // local、s3
$orm_image_uploader['default_version'] = array ('' => array ());

$orm_image_uploader['base_directory']['local'] = array ('upload');
$orm_image_uploader['separate_symbol'] = '_';
$orm_image_uploader['unique_column'] = 'id';
