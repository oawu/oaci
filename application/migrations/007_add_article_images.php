<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class Migration_Add_article_images extends CI_Migration {
  public function up () {
    $this->db->query (
      "CREATE TABLE `article_images` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `article_id` int(11) unsigned NOT NULL COMMENT 'Work ID',
        `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '檔案名稱',
        `updated_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '更新時間',
        `created_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '新增時間',
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
    );
  }
  public function down () {
    $this->db->query (
      "DROP TABLE `article_images`;"
    );
  }
}