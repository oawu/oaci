{<{<{ defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2015 OA Wu Design
 */

class Migration_<?php echo ucfirst ($action);?>_<?php echo pluralize ($name);?> extends CI_Migration {
  public function up () {
    $this->db->query (
      "CREATE TABLE `tag_article_maps` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `tag_id` int(11) NOT NULL,
        `article_id` int(11) NOT NULL,
        `created_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "',
        `updated_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "',
        PRIMARY KEY (`id`),
        KEY `tag_id_index` (`tag_id`),
        KEY `article_id_index` (`article_id`),
        UNIQUE KEY `tag_id_article_id_unique` (`tag_id`, `article_id`),
        FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE,
        FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
    );
  }
  public function down () {
    $this->db->query (
      "DROP TABLE `tag_article_maps`;"
    );
  }
}