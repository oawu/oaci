<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2015 OA Wu Design
 */

class Event extends OaModel {

  static $table_name = 'events';

  static $has_one = array (
    array ('first_attendee', 'class_name' => 'Attendee', 'order' => 'id ASC'),
  );

  static $has_many = array (
    array ('tag_event_maps', 'class_name' => 'TagEventMap'),

    array ('attendees', 'class_name' => 'Attendee'),
    array ('tags', 'class_name' => 'Tag', 'through' => 'tag_event_maps')
  );

  static $belongs_to = array (
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);

    OrmImageUploader::bind ('cover', 'EventCoverUploader');
  }
}