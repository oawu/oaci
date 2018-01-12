<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

return array (
    'up' => "CREATE TABLE `users` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '名稱',
        `account` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '帳號',
        `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '密碼',
        
        `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Access Token',
        
        `fid` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Facebook UID',
        
        `logined_cnt` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '登入次數',
        `logined_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '登入時間',

        `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時間',
        `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '新增時間',
        PRIMARY KEY (`id`),
        KEY `token_index` (`token`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;",
    
    'down' => "DROP TABLE `users`;",
    
    'at' => "2018-01-12 15:54:39",
  );