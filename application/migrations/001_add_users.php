<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Migration_Add_users extends CI_Migration {
  public function up () {
    $this->db->query (
      "CREATE TABLE `users` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,

        `fid` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Facebook UID',

        `account` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '帳號',
        `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '密碼 md5(psd + acc)',

        `token` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Access Token md5(id+time())',

        `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '名稱',
        `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '電子郵件',

        `login_count` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '登入次數',
        `logined_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '上次登入時間',
        
        `updated_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '更新時間',
        `created_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '新增時間',
        PRIMARY KEY (`id`),
        KEY `token_index` (`token`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
    );
  }
  public function down () {
    $this->db->query (
      "DROP TABLE `users`;"
    );
  }
}