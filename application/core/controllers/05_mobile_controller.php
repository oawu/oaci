<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2014 OA Wu Design
 */
class Mobile_controller extends Oa_controller {
  public function __construct () {
    parent::__construct ();
    $this->load->helper ('identity');

    $this->init_component_lists ('meta', 'css', 'javascript', 'hidden')
         ->set_componemt_path ('component', 'mobile')
         ->set_frame_path ('frame', 'mobile')
         ->set_content_path ('content', 'mobile')
         ->set_public_path ('public')
         ->set_title ('<cg>M</cg><icg>Y</icg><cg>W</cg><icg>ALL</icg>')
         ->add_meta (array ('name' => 'viewport', "content" => "width=device-width, initial-scale=1"))
         
         ->_add_css ()
         ->_add_javascript ()
         ->_add_footer ()
         ->add_hidden (array ('id' => '_fb_sign_in_message', 'value' => identity ()->get_session ('_fb_sign_in_message', true)))
         ->add_hidden (array ('id' => '_current_uri', 'value' => url_parse (current_url (), 'path')))
         ->add_hidden (array ('id' => '_search_url', 'value' => base_url (array ('tags', 'search'))))
         ;
  }

  private function _add_css () {
    return $this->add_css (base_url (utilitySameLevelPath (REL_PATH_CSS, 'bootstrap_v3.0.0', 'bootstrap.css')))
                ->add_css (base_url (utilitySameLevelPath (REL_PATH_CSS, 'bootstrap_v3.0.0', 'bootstrap-glyphicons.min.css')))
                
                ->add_css (base_url (utilitySameLevelPath (REL_PATH_CSS, 'jquery.mobile_v1.4.4', 'jquery.mobile-1.4.4.min.css')))
                ->add_css (base_url (utilitySameLevelPath (REL_PATH_CSS, 'jquery-ui-1.10.3.custom', 'redmond', 'jquery-ui-1.10.3.custom.min.css')))
                ->add_css (base_url (utilitySameLevelPath (REL_PATH_CSS, 'icomoon_d20141019', 'style.css')))
                ->add_css (base_url (utilitySameLevelPath (REL_PATH_CSS, 'timepicker', 'jquery-ui-timepicker-addon.css')))
         ;
  }
  private function _add_javascript () {
    return $this->add_javascript (base_url (utilitySameLevelPath (REL_PATH_JS, 'jquery_v1.10.2', 'jquery-1.10.2.min.js')))
                ->add_javascript (base_url (utilitySameLevelPath (REL_PATH_JS, 'jquery-ui-1.10.3.custom', 'jquery-ui-1.10.3.custom.min.js')))
                ->add_javascript (base_url (utilitySameLevelPath (REL_PATH_JS, 'jquery.mobile_v1.4.4', 'jquery.mobile-1.4.4.min.js')))
                ->add_javascript (base_url (utilitySameLevelPath (REL_PATH_JS, 'imgLiquid_v0.9.944', 'imgLiquid-min.js')))
                ->add_javascript (base_url (utilitySameLevelPath (REL_PATH_JS, 'jquery-timeago_v1.3.1', 'jquery.timeago.js')))
                ->add_javascript (base_url (utilitySameLevelPath (REL_PATH_JS, 'jquery-timeago_v1.3.1', 'locales', 'jquery.timeago.zh-TW.js')))
                ->add_javascript (base_url (utilitySameLevelPath (REL_PATH_JS . 'masonry_v3.1.2', 'masonry.pkgd.min.js')))
                ->add_javascript (base_url (utilitySameLevelPath (REL_PATH_JS, 'imagesloaded_v3.1.8', 'imagesloaded.pkgd.min.js')))
                ->add_javascript (base_url (utilitySameLevelPath (REL_PATH_JS, 'underscore_v1.7.0', 'underscore-min.js')))
         ;
  }
  private function _add_footer () {
    return $this;
  }
}