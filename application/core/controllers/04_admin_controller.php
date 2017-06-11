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
         ->add_hidden (array ('id' => 'filebrowserUploadUrl', 'value' => base_url ('admin', 'ckeditor', 'image_upload')))
         ->add_hidden (array ('id' => 'filebrowserImageBrowseUrl', 'value' => base_url ('admin', 'ckeditor', 'image_browser')))
         ;

    if (file_exists ($path = FCPATH . implode (DIRECTORY_SEPARATOR, array_merge ($this->get_views_path (), $this->get_public_path (), array ('icon_admin.css')))) && is_readable ($path))
      $this->add_css (base_url (implode ('/', array_merge ($this->get_views_path (), $this->get_public_path (), array ('icon_admin.css')))));
  }

  protected function _get_pagination ($configs) {
    $this->load->library ('pagination');
    return $this->pagination->initialize (array_merge (array ('num_links' => 3, 'uri_segment' => 0, 'base_url' => '', 'page_query_string' => false, 'first_link' => '', 'last_link' => '', 'prev_link' => '', 'next_link' => '', 'full_tag_open' => '<ul>', 'full_tag_close' => '</ul>', 'first_tag_open' => '<li class="f icon-first_page">', 'first_tag_close' => '</li>', 'prev_tag_open' => '<li class="p icon-keyboard_arrow_left">', 'prev_tag_close' => '</li>', 'num_tag_open' => '<li>', 'num_tag_close' => '</li>', 'cur_tag_open' => '<li class="active"><a href="#">', 'cur_tag_close' => '</a></li>', 'next_tag_open' => '<li class="n icon-keyboard_arrow_right">', 'next_tag_close' => '</li>', 'last_tag_open' => '<li class="l icon-last_page">', 'last_tag_close' => '</li>'), $configs))->create_links ();
  }
  private function _add_meta () {
    return $this;
  }

  private function _add_css () {
    return $this;
  }

  private function _add_js () {
    return $this->add_js (res_url ('resource', 'javascript', 'jquery_v1.10.2', 'jquery-1.10.2.min.js'))
                ->add_js (res_url ('resource', 'javascript', 'jquery-rails_d2015_03_09', 'jquery_ujs.js'))
                ->add_js (res_url ('resource', 'javascript', 'jquery-timeago_v1.3.1', 'oas.js'))
                ->add_js (res_url ('resource', 'javascript', 'imgLiquid_v0.9.944', 'imgLiquid-min.js'))
                ->add_js (res_url ('resource', 'javascript', 'ckeditor_d2015_05_18', 'ckeditor.js'), false)
                ->add_js (res_url ('resource', 'javascript', 'ckeditor_d2015_05_18', 'config.js'), false)
                ->add_js (res_url ('resource', 'javascript', 'ckeditor_d2015_05_18', 'adapters', 'jquery.js'), false)
                ;
  }
}