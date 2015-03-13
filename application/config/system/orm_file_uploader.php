<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2015 OA Wu Design
 */

$orm_file_uploader['debug'] = false;

$orm_file_uploader['instance']['class_suffix'] = 'FileUploader';
$orm_file_uploader['instance']['directory'] = array ('application', 'third_party', 'orm_file_uploaders');

$orm_file_uploader['unique_column'] = 'id';
$orm_file_uploader['bucket'] = 'local'; // local、s3

$orm_file_uploader['base_directory']['local'] = array ('upload');
$orm_file_uploader['d4_url'] = '';
