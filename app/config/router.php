<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

// Router::get ('', 'main');

Router::restful ('tags', 'tags', array (
  array ('model' => 'Tag', 'where' => array ('status = ?', Tag::STATUS_1))));

// Router::restful ('tags', 'tags', 'Tag');

// Router::restful (array ('tag', 'articles'), 'tag_articles', array ('Tag', 'Article'));
// Router::restful (array ('tag', 'article', 'comments'), 'tag_articles', array ('Tag', 'Tag', 'Tag'));
// Router::post ('banner', 'banner@index');

Router::dir ('admin', function () {
  // Router::post ('banner', 'banner@index');
  // Router::restful ('tags', 'tags', 'Tag');
  // Router::restful (array ('tag', 'articles'), 'tag_articles', array ('Tag', 'Article'));
  // Router::restful (array ('tag', 'article', 'comments'), 'tag_articles', array ('Tag', 'Tag', 'Tag'));

// Router::restful ('articles', 'articles');
// Router::restful (array ('articles'), 'articles');
// Router::restful (array ('tag', 'articles'), 'articles');
});

// print (json_encode(Router::$routers));
// exit ();