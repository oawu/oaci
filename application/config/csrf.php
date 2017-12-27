<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

return array (
  'protection' => false,

  'expire' => 7200,
  'token_name' => 'csrf_test_name',
  'cookie_name' => 'csrf_cookie_name',

  'regenerate' => true,
  'exclude_uris' => array()
);