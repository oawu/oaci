<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Admin_controller extends Oa_controller {

  public function __construct () {
    parent::__construct ();

    if (!(User::current () && User::current ()->is_login ()))
      return redirect_message (array ('login'), array ());

    $this
         ->set_componemt_path ('component', 'admin')
         ->set_frame_path ('frame', 'admin')
         ->set_content_path ('content', 'admin')
         ->set_public_path ('public')

         ->set_title ("OA's CI")

         ->_add_meta ()
         ->_add_css ()
         ->_add_js ()
         ;

    if (file_exists ($path = FCPATH . implode (DIRECTORY_SEPARATOR, array_merge ($this->get_views_path (), $this->get_public_path (), array ('icon_admin.css')))) && is_readable ($path))
      $this->add_css (base_url (implode ('/', array_merge ($this->get_views_path (), $this->get_public_path (), array ('icon_admin.css')))));
  }

  private function _add_meta () {
    return $this;
  }

  private function _add_css () {
    return $this;
  }

  private function _add_js () {
    return $this->add_js (base_url ('resource', 'javascript', 'jquery_v1.10.2', 'jquery-1.10.2.min.js'))
                ->add_js (base_url ('resource', 'javascript', 'jquery-rails_d2015_03_09', 'jquery_ujs.js'))
                ->add_js (base_url ('resource', 'javascript', 'imgLiquid_v0.9.944', 'imgLiquid-min.js'))
                ;
  }
}