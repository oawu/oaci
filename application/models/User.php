<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class User extends OaModel {

  static $table_name = 'users';

  static $has_one = array (
  );

  static $has_many = array (
    array ('roles', 'class_name' => 'UserRole'),
  );

  static $belongs_to = array (
  );

  private static $current = '';
  
  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }
  public static function current () {
    if (self::$current !== '') return self::$current;
    return self::$current = ($token = Session::getData ('user_token')) ? User::find_by_token ($token) : null;
  }
  public function is_root () {
    return $this->roles && in_array ('root', column_array ($this->roles, 'name'));
  }
  public function is_login () {
    if (!$this->roles) return false;
    if ($this->is_root ()) return true;
    return in_array ('member', column_array ($this->roles, 'name'));
  }
  public function in_roles ($roles = array ()) {
    if (!$this->roles) return false;
    if ($this->is_root ()) return true;
    if (!($roles = array_filter ($roles, function ($role) { return in_array ($role, Cfg::setting ('role', 'roles')); }))) return false;
    foreach ($this->roles as $role) if (in_array ($role->name, $roles)) return true;
    return false;
  }
  public function role_names () {
    return array_filter (array_map (function ($role) { return Cfg::setting ('role', 'role_names', $role); }, column_array ($this->roles, 'name')));
  }
  // public function facebook_link () {
  //   if (!isset ($this->fid)) return '';
  //   return 'https://www.facebook.com/' . $this->fid;
  // }
  public function avatar ($w = 100, $h = 100) {
    if ($this->fid) {
      $size = array ();
      array_push ($size, isset ($w) && $w ? 'width=' . $w : ''); array_push ($size, isset ($h) && $h ? 'height=' . $h : '');
      return 'https://graph.facebook.com/' . $this->fid . '/picture' . (($size = implode ('&', array_filter ($size))) ? '?' . $size : '');
    }

    return res_url ('resource', 'image', 'avatar.png');
  }
}