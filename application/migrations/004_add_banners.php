<?php if (!defined ('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Migration_Add_banners extends CI_Migration {
  public function up () {
    $this->db->query (
      "CREATE TABLE `banners` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,

        `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '標題',
        `content` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '內容',
        `href` text  COMMENT '網址',
        `cover` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '封面',

        `sort` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '排列順序，上至下 DESC',
        `target` tinyint(4) unsigned NOT NULL DEFAULT 1 COMMENT '鏈結開啟方式，1 本頁，2 分頁',
        `status` tinyint(4) unsigned NOT NULL DEFAULT 1 COMMENT '上下架，1 下架，2 上架',

        `updated_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '更新時間',
        `created_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '新增時間',
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
    );
  }
  public function down () {
    $this->db->query (
      "DROP TABLE `banners`;"
    );
  }
}