<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

return array (
    'up' => "CREATE TABLE `article_tag_mappings` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        
        `article_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'Article ID',
        `tag_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'Tag ID',

        `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時間',
        `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '新增時間',
        PRIMARY KEY (`id`),
        KEY `article_id_index` (`article_id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;",
    
    'down' => "DROP TABLE `article_tag_mappings`;",
    
    'at' => "2018-01-18 01:06:23",
  );