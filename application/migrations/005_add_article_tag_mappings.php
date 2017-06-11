<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class Migration_Add_article_tag_mappings extends CI_Migration {
  public function up () {
    $this->db->query (
      "CREATE TABLE `article_tag_mappings` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `article_id` int(11) unsigned NOT NULL COMMENT 'Article ID',
        `article_tag_id` int(11) unsigned NOT NULL COMMENT 'Article Tag ID',
        `updated_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '更新時間',
        `created_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '新增時間',
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
    );
  }
  public function down () {
    $this->db->query (
      "DROP TABLE `article_tag_mappings`;"
    );
  }
}