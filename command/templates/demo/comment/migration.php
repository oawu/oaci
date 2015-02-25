 defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2015 OA Wu Design
 */

class Migration_<?php echo ucfirst ($action);?>_<?php echo pluralize ($name);?> extends CI_Migration {
  public function up () {
    $this->db->query (
      "CREATE TABLE `comments` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `user_id` int(11) NOT NULL,
        `article_id` int(11) NOT NULL,
        `content` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
        `created_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "',
        `updated_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "',
        PRIMARY KEY (`id`),
        KEY `user_id_index` (`user_id`),
        KEY `article_id_index` (`article_id`),
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
        FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
    );
  }
  public function down () {
    $this->db->query (
      "DROP TABLE `comments`;"
    );
  }
}