{<{<{ if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2015 OA Wu Design
 */

class <?php echo ucfirst (camelize ($name));?> extends OaModel {

  static $table_name = '<?php echo pluralize ($name);?>';

  static $has_one = array (
    array ('last_article', 'class_name' => 'Article', 'order' => 'id DESC'),
  );

  static $has_many = array (
    array ('articles', 'class_name' => 'Article')
  );

  static $belongs_to = array (
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