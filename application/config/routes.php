<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

Route::root ('main');

Route::get ('/login', 'platform@login');
Route::get ('/logout', 'platform@logout');

Route::get ('admin', 'admin/main@index');

Route::group ('admin', function () {
  Route::resourcePagination (array ('article_tags'), 'article_tags');
  Route::resourcePagination (array ('articles'), 'articles');
});
