<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

return array (
    'up' => "CREATE TABLE `articles` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `user_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'User ID(作者)',
        
        `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '標題',
        `content` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '內容',
        `cover` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '封面',

        `lat` DOUBLE NOT NULL COMMENT '緯度',
        `lng` DOUBLE NOT NULL COMMENT '經度',

        `action` enum('_self','_target') COLLATE utf8_unicode_ci NOT NULL DEFAULT '_target' COMMENT '開啟方式，本頁、分頁',
        `status` enum('on','off') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'off' COMMENT '狀態，上線、下線',

        `sort` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '排列順序，上至下 DESC',

        `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時間',
        `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '新增時間',
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;",
    
    'down' => "DROP TABLE `articles`;",
    
    'at' => "2018-01-18 01:06:23",
  );