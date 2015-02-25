 if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2015 OA Wu Design
 */

class <?php echo ucfirst (camelize ($name));?> extends OaModel {

  static $table_name = '<?php echo pluralize ($name);?>';

  static $has_one = array (
    array ('last_comment', 'class_name' => 'Comment', 'order' => 'id DESC'),
  );

  static $has_many = array (
    array ('tag_article_maps', 'class_name' => 'TagArticleMap'),

    array ('comments', 'class_name' => 'Comment'),
    array ('tags', 'class_name' => 'Tag', 'through' => 'tag_article_maps')
  );

  static $belongs_to = array (
    array ('user', 'class_name' => 'User'),
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
<?php
    if ($columns) { ?>

<?php
      foreach ($columns as $column) { ?>
    OrmImageUploader::bind ('<?php echo $column; ?>', '<?php echo ucfirst (camelize ($name)) . ucfirst ($column) . 'Uploader'; ?>');
<?php
      }
    } ?>
  }
}