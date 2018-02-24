<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

Router::get ('login', 'main@login');
Router::get ('logout', 'main@logout');

Router::get ('fb_signin', 'main@fb_signin');
Router::post ('login', 'main@ac_signin');

Router::dir ('admin', function () {
  Router::get ('', 'main');

  Router::restful ('tags', 'tags', array (
    array ('model' => 'Tag')));
  
  Router::restful ('articles', 'articles', array (
    array ('model' => 'Article')));
});