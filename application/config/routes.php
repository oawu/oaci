<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
Route::root ('main');

Route::get ('admin', 'admin/main@index');
