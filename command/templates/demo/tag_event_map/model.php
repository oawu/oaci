{<{<{ if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2015 OA Wu Design
 */

class <?php echo ucfirst (camelize ($name));?> extends OaModel {

  static $table_name = '<?php echo pluralize ($name);?>';

  static $validates_uniqueness_of = array (
    array (array ('tag_id', 'event_id'), 'message' => 'columns(tag_id, event_id) Repeat!')
  );

  static $has_one = array (
  );

  static $has_many = array (
    array ('tags', 'class_name' => 'Tag'),
    array ('events', 'class_name' => 'Event')
  );

  static $belongs_to = array (
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
<?php
    if ($columns) { ?>

<?php
      foreach ($columns as $column) { ?>
    OrmImageUploader::bind ('<?php echo $column; ?>', '<?php echo ucfirst (camelize ($name)) . ucfirst ($column) . $uploader_class_suffix; ?>');
<?php
      }
    } ?>
  }
}