<?php if (!defined ('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Migration_Add_article_sources extends CI_Migration {
  public function up () {
    $this->db->query (
      "CREATE TABLE `article_sources` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `article_id` int(11) unsigned NOT NULL COMMENT 'Article ID',
        `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '標題',
        `href` text  COMMENT '網址',
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
    );
  }
  public function down () {
    $this->db->query (
      "DROP TABLE `article_sources`;"
    );
  }
}