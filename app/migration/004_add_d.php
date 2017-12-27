<?php if (!defined ('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

return array (
    'up' => array (
        "CREATE TABLE `d` (
          `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
          `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '標題',
          `content` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '內容',
          `href` text COLLATE utf8_unicode_ci COMMENT '網址',
          `cover` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '封面',
          `sort` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '排列順序，上至下 DESC',
          `target` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '鏈結開啟方式，1 本頁，2 分頁',
          `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '上下架，1 下架，2 上架',
          `updated_at` datetime NOT NULL DEFAULT '2017-11-25 15:50:11' COMMENT '更新時間',
          `created_at` datetime NOT NULL DEFAULT '2017-11-25 15:50:11' COMMENT '新增時間',
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
      ),
    'down' => "DROP TABLE `d`;",
    'at' => '2017-12-01 12:23:34'
  );