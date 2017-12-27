<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2017, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

Router::get ('', 'main');

Router::group ('admin', function () {
  Router::resource (array ('articles'), 'articles');
  Router::resource (array ('tag', 'articles'), 'tag_articles');
});
