<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2014 OA Wu Design
 */
class Oa_controller extends Root_controller {
  private $componemt_path = array ();
  private $frame_path     = array ();
  private $content_path   = array ();
  private $public_path    = array ();

  private $title = '';

  private $component_lists = array ();

  public function __construct () {
    parent::__construct ();

    $this->init_component_lists ('meta', 'css', 'javascript', 'hidden')
         ->add_meta (array ('http-equiv' => 'Content-type', "content" => "text/html; charset=utf-8"));
  }

  protected function init_component_lists () {
    if (!$this->component_lists)
      $this->component_lists = array ();
    if ($components = func_get_args ())
      foreach ($components as $component)
        if (!isset ($this->component_lists[$component]))
          $this->component_lists[$component] = array ();
    return $this;
  }

  protected function clean_component_lists () {
    if (($args = array_filter (func_get_args ())) || ($args = array_keys ($this->component_lists)))
      foreach ($args as $arg)
        unset ($this->component_lists[$arg]);
    return $this;
  }

  protected function set_componemt_path () {
    $this->componemt_path = array_filter (func_get_args ());
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

  protected function add_component_list ($key, $value, $my_key = null) {
    if (isset ($this->component_lists[$key]))
      if ($my_key)
        $this->component_lists[$key][$my_key][] = $value;
      else
        array_push ($this->component_lists[$key], $value);
    return $this;
  }

  public function add_javascript () {
    if ($path = array_filter (func_get_args ()))
      $this->add_component_list ('javascript', implode (DIRECTORY_SEPARATOR, $path));
    return $this;
  }

  public function add_css () {
    if ($path = array_filter (func_get_args ()))
      $this->add_component_list ('css', implode (DIRECTORY_SEPARATOR, $path));
    return $this;
  }

  protected function add_hidden ($attributes = array ()) {
    if ($attributes)
      $this->add_component_list ('hidden', $attributes);
    return $this;
  }

  protected function add_meta ($attributes = array ()) {
    if ($attributes)
      $this->add_component_list ('meta', $attributes);
    return $this;
  }

  protected function add_footer ($title, $sub_items = array ()) {
    if (($args = func_get_args ()) && ($title = array_shift ($args)) && ($sub_itemss = $args))
      foreach ($sub_itemss  as $sub_items)
        $this->add_component_list ('footer', $sub_items, $title);
    return $this;
  }

  public function get_componemt_path () {
    return $this->componemt_path;
  }

  public function get_public_path () {
    return $this->public_path;
  }

  public function get_title () {
    return $this->title;
  }

  public function get_component_lists () {
    return $this->component_lists;
  }

  protected function has_post () {
    return ($this->input->post () !== false) && $_POST;
  }

  protected function is_ajax ($has_post = true) {
    return $this->input->is_ajax_request () && (!$has_post || $this->has_post ());
  }

  protected function output_json ($data, $cache = 0) {
    $this->output->set_content_type ('application/json')->set_output (json_encode ($data))->cache ($cache);
  }

  protected function load_components () {
    $frame_data = array ();
    $component_lists_keys = array_keys ($component_lists = $this->get_component_lists ());
    if ($components = !($components = array_filter (func_get_args ())) ? $component_lists_keys : array_filter ($components, function ($component) use ($component_lists_keys) { return in_array ($component, $component_lists_keys); }))
      foreach ($components as $component)
        if (is_readable (utilitySameLevelPath (FCPATH . APPPATH . DIRECTORY_SEPARATOR . implode (DIRECTORY_SEPARATOR, $this->get_views_path ()) . DIRECTORY_SEPARATOR . ($path = utilitySameLevelPath (implode (DIRECTORY_SEPARATOR, $this->get_componemt_path ()) . DIRECTORY_SEPARATOR . $component . EXT)))))
          $frame_data[$component] = $this->load->view ($path, array ($component . '_list' => $this->component_lists[$component]), true);
    return $frame_data;
  }

  protected function load_view ($data = '', $return = false, $cache_time = 0) {
    if (is_readable ($abs_path = utilitySameLevelPath (FCPATH . APPPATH . DIRECTORY_SEPARATOR . implode (DIRECTORY_SEPARATOR, $this->get_views_path ()) . DIRECTORY_SEPARATOR . ($path = utilitySameLevelPath (implode (DIRECTORY_SEPARATOR, array_merge ($this->get_frame_path (), array ('frame.php')))))))) {
      if ($this->get_class () && $this->get_method ()) {

        $this->add_css (base_url (utilitySameLevelPath (implode (DIRECTORY_SEPARATOR, array_merge (array (APPPATH), $this->get_views_path (), $this->get_public_path (), array ('public.css'))))))
             ->add_css (base_url (utilitySameLevelPath (implode (DIRECTORY_SEPARATOR, array_merge (array (APPPATH), $this->get_views_path (), $this->get_frame_path (), array ('frame.css'))))))
             ->add_css (base_url (utilitySameLevelPath (implode (DIRECTORY_SEPARATOR, array_merge (array (APPPATH), $this->get_views_path (), $this->get_content_path (), array ($this->get_class (), $this->get_method (), 'content.css'))))))

             ->add_javascript (base_url (utilitySameLevelPath (implode (DIRECTORY_SEPARATOR, array_merge (array (APPPATH), $this->get_views_path (), $this->get_public_path (), array ('public.js'))))))
             ->add_javascript (base_url (utilitySameLevelPath (implode (DIRECTORY_SEPARATOR, array_merge (array (APPPATH), $this->get_views_path (), $this->get_frame_path (), array ('frame.js'))))))
             ->add_javascript (base_url (utilitySameLevelPath (implode (DIRECTORY_SEPARATOR, array_merge (array (APPPATH), $this->get_views_path (), $this->get_content_path (), array ($this->get_class (), $this->get_method (), 'content.js'))))));
        
        $frame_data = array ();
        $frame_data = array_merge ($frame_data, $this->load_components ());
        $frame_data['title']   = $this->get_title ();
        $frame_data['content'] = $this->load_content ($data, true);

        if ($return) return $this->load->view ($path, $frame_data, $return);
        else $this->load->view ($path, $frame_data, $return)->cache ($cache_time);
      } else {
        show_error ('The controller lack of necessary resources!!  Please confirm your program again.');
      }
    } else {
      show_error ('Can not find frame file. path: ' . $abs_path);
    }
  }
}