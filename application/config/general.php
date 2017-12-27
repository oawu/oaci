<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

return array (
  'base_url' => '',
  'encryption_key' => '',
  'composer_autoload' => false,
  'charset' => 'UTF-8',
  
  'index_page' => 'index.php',
  'url_suffix' => '',
  'language'	=> 'english',
  'enable_hooks' => false,
  'subclass_prefix' => 'MY_',
  'permitted_uri_chars' => 'a-z 0-9~%.:_\-',

  'enable_query_strings' => false,
  'controller_trigger' => 'c',
  'function_trigger' => 'm',
  'directory_trigger' => 'd',

  'allow_get_array' => true,

  'cache_path' => '',
  'cache_query_string' => false,

  'sess_driver' => 'files',
  'sess_cookie_name' => 'ci_session',
  'sess_expiration' => 7200,
  'sess_save_path' => null,
  'sess_match_ip' => false,
  'sess_time_to_update' => 300,
  'sess_regenerate_destroy' => false,

  'cookie_prefix'	=> '',
  'cookie_domain'	=> '',
  'cookie_path'		=> '/',
  'cookie_secure'	=> false,
  'cookie_httponly' 	=> false,

  'standardize_newlines' => false,

  'global_xss_filtering' => false,

  'compress_output' => false,
  'time_reference' => 'local',
  'rewrite_short_tags' => false,

  'proxy_ips' => '',
);