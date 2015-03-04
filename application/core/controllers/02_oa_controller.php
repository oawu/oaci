<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2015 OA Wu Design
 */

class Oa_controller extends Root_controller {
  // private $component_lists = array ();
  private $componemt_path  = array ();
  private $frame_path      = array ();
  private $content_path    = array ();
  private $public_path     = array ();
  private $title           = '';

  private $meta_list   = array ();
  private $hidden_list = array ();
  private $js_list     = array ();
  private $css_list    = array ();

  public function __construct () {
    parent::__construct ();
    $this->add_meta (array ('http-equiv' => 'Content-type', 'content' => 'text/html; charset=utf-8'));
  }

  protected function set_componemt_path () {
    $this->componemt_path = array_filter (func_get_args ());
    return $this;
  }

  protected function set_frame_path () {
    $this->frame_path = array_filter (func_get_args ());
    return $this;
  }

  protected function set_content_path () {
    $this->content_path = array_filter (func_get_args ());
    return $this;
  }

  protected function set_public_path () {
    $this->public_path = array_filter (func_get_args ());
    return $this;
  }

  protected function set_title ($title) {
    $this->title = $title;
    return $this;
  }

  protected function add_meta ($attributes) {
    array_push ($this->meta_list, $attributes);
    return $this;
  }

  protected function add_hidden ($attributes) {
    array_push ($this->hidden_list, $attributes);
    return $this;
  }

  public function add_js ($path, $is_minify = true) {
    array_push ($this->js_list, array ('path' => $path, 'is_minify' => $is_minify));
    return $this;
  }

  public function add_css ($path, $is_minify = true) {
    array_push ($this->css_list, array ('path' => $path, 'is_minify' => $is_minify));
    return $this;
  }

  public function get_componemt_path () {
    return $this->componemt_path;
  }

  public function get_frame_path () {
    return $this->frame_path;
  }

  public function get_content_path () {
    return $this->content_path;
  }

  public function get_public_path () {
    return $this->public_path;
  }

  public function get_title () {
    return $this->title;
  }

  protected function has_post () {
    return ($this->input->post () !== false) && $_POST;
  }

  protected function is_ajax ($has_post = true) {
    return $this->input->is_ajax_request () && (!$has_post || $this->has_post ());
  }

  protected function output_json ($data, $cache = 0) {
    return $this->output
                ->set_content_type ('application/json')
                ->set_output (json_encode ($data))
                ->cache ($cache);
  }

  protected function load_components () {
    $list = array (
        'meta_list'   => array_filter ($this->meta_list),
        'hidden_list' => array_filter ($this->hidden_list),
        'js_list'  => array_filter (array_map (function ($js) { return $js['path']; }, $this->js_list)),
        'css_list' => array_filter (array_map (function ($css) { return $css['path']; }, $this->css_list))
      );
    $frame_data = array ();

    foreach ($list as $key => $value)
      if (is_readable (FCPATH . APPPATH . implode (DIRECTORY_SEPARATOR, array_merge ($this->get_views_path (), $this->get_componemt_path (), array ($key . EXT)))))
        $frame_data[$key] = $this->load->view (implode (DIRECTORY_SEPARATOR, array_merge ($this->get_componemt_path (), array ($key . EXT))), array ($key => $value), true);

    return $frame_data;
  }

  private function _combine_static_files () {
    if (ENVIRONMENT !== 'production')
      return $this;

    if (!is_writable ($folder_path = FCPATH . implode (DIRECTORY_SEPARATOR, Cfg::system ('static', 'assets_folder'))))
      return $this;

    $this->load->driver ('minify');

    if ($component_lists = array_map (function ($component_list) { return array_filter ($component_list, function ($component) { return preg_match ("|^(" . preg_quote (base_url ()) . ")|", $component); }); }, array_intersect_key ($this->get_component_lists (), array_flip (Cfg::system ('static', 'allow_keys'))))) {
      foreach ($component_lists as $key => $component_list) {

        if (Cfg::system ('static', 'enable') && is_readable ($path = implode (DIRECTORY_SEPARATOR, array ($folder_path, Cfg::system ('static', 'file_prefix') . get_parent_class ($this) . '_|_' . $this->get_class () . '_|_' . $this->get_method () . '.' . $key)))) {
          $this->component_lists[$key] = array_diff ($this->component_lists[$key], $component_list);
          array_push ($this->component_lists[$key], base_url (array (implode ('/', Cfg::system ('static', 'assets_folder')), Cfg::system ('static', 'file_prefix') . get_parent_class ($this) . '_|_' . $this->get_class () . '_|_' . $this->get_method () . '.' . $key)));
          continue;
        }

        $data = Cfg::system ('static', 'minify') ? $this->minify->$key->min (implode ('', array_map (function ($component) { return read_file (FCPATH . preg_replace ("|^(" . preg_quote (base_url ()) . ")|", '', $component)); }, $component_list))) : implode ('', array_map (function ($component) { return read_file (FCPATH . preg_replace ("|^(" . preg_quote (base_url ()) . ")|", '', $component)); }, $component_list));
        $path = implode (DIRECTORY_SEPARATOR, array ($folder_path, Cfg::system ('static', 'file_prefix') . get_parent_class ($this) . '_|_' . $this->get_class () . '_|_' . $this->get_method () . '.' . $key));

        if (write_file ($path, $data, 'w+')) {
          $this->component_lists[$key] = array_diff ($this->component_lists[$key], $component_list);
          array_push ($this->component_lists[$key], base_url (array (implode ('/', Cfg::system ('static', 'assets_folder')), Cfg::system ('static', 'file_prefix') . get_parent_class ($this) . '_|_' . $this->get_class () . '_|_' . $this->get_method () . '.' . $key)));
        }
      }
    }
    return $this;
  }

  protected function load_view ($data = '', $return = false, $cache_time = 0) {
    if (!is_readable ($path = FCPATH . APPPATH . implode (DIRECTORY_SEPARATOR, array_merge ($this->get_views_path (), $this->get_frame_path (), array ('frame' . EXT)))))
      return show_error ('Can not find frame file. path: ' . $path);
    else
      $frame_path = implode (DIRECTORY_SEPARATOR, array_merge ($this->get_frame_path (), array ('frame' . EXT)));

    if (!($this->get_class () && $this->get_method ()))
      return show_error ('The controller lack of necessary resources!!  Please confirm your program again.');

    $this->add_css (base_url (APPPATH . implode (DIRECTORY_SEPARATOR, array_merge ($this->get_views_path (), $this->get_public_path (), array ('public.css')))))
         ->add_css (base_url (APPPATH . implode (DIRECTORY_SEPARATOR, array_merge ($this->get_views_path (), $this->get_public_path (), array ('frame.css')))))
         ->add_css (base_url (APPPATH . implode (DIRECTORY_SEPARATOR, array_merge ($this->get_views_path (), $this->get_public_path (), array ($this->get_class (), $this->get_method (), 'content.css')))))
         ->add_js (base_url (APPPATH . implode (DIRECTORY_SEPARATOR, array_merge ($this->get_views_path (), $this->get_public_path (), array ('public.js')))))
         ->add_js (base_url (APPPATH . implode (DIRECTORY_SEPARATOR, array_merge ($this->get_views_path (), $this->get_public_path (), array ('frame.js')))))
         ->add_js (base_url (APPPATH . implode (DIRECTORY_SEPARATOR, array_merge ($this->get_views_path (), $this->get_public_path (), array ($this->get_class (), $this->get_method (), 'content.js')))));

    $this->_combine_static_files ();

    $frame_data = array ();
    $frame_data = array_merge ($frame_data, $this->load_components ());
    $frame_data['title']   = $this->get_title ();
    $frame_data['content'] = $this->load_content ($data, true);

    if ($return) return $this->load->view ($frame_path, $frame_data, $return);
    else $this->load->view ($frame_path, $frame_data, $return)->cache ($cache_time);
  }
}