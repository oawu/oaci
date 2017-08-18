<?php if (!defined ('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
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

  private $append_js_list      = array ();
  private $append_css_list     = array ();
  private $static_file_version = 10;

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
    if (isset ($attributes['name']))
      $this->meta_list = array_filter ($this->meta_list, function ($meta) use ($attributes) { return !isset ($meta['name']) || ($meta['name'] != $attributes['name']);});

    if (isset ($attributes['property']))
      $this->meta_list = array_filter ($this->meta_list, function ($meta) use ($attributes) { return !isset ($meta['property']) || ($meta['property'] != $attributes['property']) || isset ($meta['tag']) && ($meta['tag'] != $attributes['tag']);});

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

  public function append_js ($path, $is_minify = true) {
    array_push ($this->append_js_list, array ('path' => $path, 'is_minify' => $is_minify));
    return $this;
  }

  public function append_css ($path, $is_minify = true) {
    array_push ($this->append_css_list, array ('path' => $path, 'is_minify' => $is_minify));
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
    $this->_combine_static_files ();

    $list = array (
        'meta_list'   => array_filter ($this->meta_list),
        'hidden_list' => array_filter ($this->hidden_list),
        'js_list'  => array_unique (array_filter (array_map (function ($js) { return isset ($js['path']) ? $js['path'] : $js; }, $this->js_list))),
        'css_list' => array_unique (array_filter (array_map (function ($css) { return isset ($css['path']) ? $css['path'] : $css; }, $this->css_list)))
      );
    $frame_data = array ();

    foreach ($list as $key => $value)
      if (is_readable (FCPATH . implode (DIRECTORY_SEPARATOR, array_merge ($this->get_views_path (), $this->get_componemt_path (), array ($key . EXT)))))
        $frame_data[$key] = $this->load->view (implode (DIRECTORY_SEPARATOR, array_merge ($this->get_componemt_path (), array ($key . EXT))), array ($key => $value), true);

    return $frame_data;
  }

  private function _build_static_files ($temp, $i, $format) {
    if (!$temp)
      return null;

    if (!is_writable ($folder_path = FCPATH . implode (DIRECTORY_SEPARATOR, Cfg::system ('static', 'assets_folder')) . DIRECTORY_SEPARATOR))
      return null;

    $file_name = implode (Cfg::system ('static', 'separate'), array (Cfg::system ('static', 'file_prefix'), get_parent_class ($this), $this->get_class (), $this->get_method (), Cfg::system ('static', 'name'), $i));
    $file_name =  $this->static_file_version . '_' . (Cfg::system ('static', 'is_md5') ? md5 ($file_name) : $file_name) . '.' .  $format;
    $bom = pack ('H*','EFBBBF');

    $cfg = Cfg::system ('static', 's3');
    $f = implode ('/', array_merge (Cfg::system ('static', 'assets_folder'), array ($file_name)));

    if (!is_readable ($folder_path . $file_name) && !($data = '')) {
      foreach ($temp as $key => $value)
        $data .= (($file = preg_replace("/^$bom/", '', read_file ($path = FCPATH . preg_replace ("|^(" . preg_quote (base_url ('')) . ")|", '', $value)))) ? Cfg::system ('static', 'minify') ? $this->minify->$format->min ($file) : $file : '') . "\n";
      write_file ($t = $folder_path . $file_name, $data, 'w+');

      
      if ($cfg) {
        if (!class_exists ('S3')) {
          $this->load->library ('S3');

          if (!S3::initialize (Cfg::system ('s3', 'buckets', $cfg['bucket'])))
            return $this->error = array ('OrmUploader 錯誤！', '初始化 S3 錯誤！', '請確認一下 Bucket 的 access_key 與 secret_key 是否正確');
        }

        $cfg = S3::putFile ($t, $cfg['bucket'], $f);
      }
    }

    return ($cfg ? $cfg['url'] . $f : base_url ($f)) . '?v=' . $this->static_file_version;
  }
  private function _combine_static_files () {
    if ((ENVIRONMENT !== 'production') && Cfg::system ('static', 'enable'))
      return $this;

    if (!is_writable ($folder_path = FCPATH . implode (DIRECTORY_SEPARATOR, Cfg::system ('static', 'assets_folder')) . DIRECTORY_SEPARATOR))
      return $this;

    $this->load->driver ('minify');

    foreach (array ('js', 'css') as  $key) {
      $list = $key . '_list';

      if ($this->$list) {
        $temp = $$key = array ();

        foreach ($this->$list as $i => $value)
          if (!$value['is_minify'] || !preg_match ("|^(" . preg_quote (base_url ('')) . ")|", $value['path']))
            if ($static_path = $this->_build_static_files ($temp, $i, $key)) array_push ($$key, $static_path, $value['path']) && $temp = array ();
            else ($$key = array_merge ($$key, $temp, array ($value['path']))) && $temp = array ();
          else array_push ($temp, $value['path']);

        if ($static_path = $this->_build_static_files ($temp, $i, $key)) array_push ($$key, $static_path);
        else $$key = array_merge ($$key, $temp);

        $this->$list = array_filter ($$key);
      }
    }

    return $this;
  }

  protected function load_view ($data = array (), $return = false, $cache_time = 0) {
    if (!is_readable ($path = FCPATH . implode (DIRECTORY_SEPARATOR, array_merge ($this->get_views_path (), $this->get_frame_path (), array ('frame' . EXT)))))
      return show_error ('Can not find frame file. path: ' . $path);
    else
      $frame_path = implode (DIRECTORY_SEPARATOR, array_merge ($this->get_frame_path (), array ('frame' . EXT)));

    if (!($this->get_class () && $this->get_method ()))
      return show_error ('The controller lack of necessary resources!!  Please confirm your program again.');

    $this->add_css (base_url (implode ('/', array_merge ($this->get_views_path (), $this->get_public_path (), array ('public.css')))))
         ->add_css (base_url (implode ('/', array_merge ($this->get_views_path (), $this->get_frame_path (), array ('frame.css')))))
         ->add_js (base_url (implode ('/', array_merge ($this->get_views_path (), $this->get_public_path (), array ('public.js')))))
         ->add_js (base_url (implode ('/', array_merge ($this->get_views_path (), $this->get_frame_path (), array ('frame.js')))));

    if (file_exists ((FCPATH . implode ('/', array_merge ($this->get_views_path (), $this->get_content_path (), array ($this->get_class (), $this->get_method (), 'content.css'))))))
      $this->add_css (base_url (implode ('/', array_merge ($this->get_views_path (), $this->get_content_path (), array ($this->get_class (), $this->get_method (), 'content.css')))));

    if (file_exists ((FCPATH . implode ('/', array_merge ($this->get_views_path (), $this->get_content_path (), array ($this->get_class (), $this->get_method (), 'content.js'))))))
      $this->add_js (base_url (implode ('/', array_merge ($this->get_views_path (), $this->get_content_path (), array ($this->get_class (), $this->get_method (), 'content.js')))));

    if ($this->append_js_list)
      foreach ($this->append_js_list as $append_js)
        $this->add_js ($append_js['path'], $append_js['is_minify']);
    
    if ($this->append_css_list)
      foreach ($this->append_css_list as $append_css)
        $this->add_css ($append_css['path'], $append_css['is_minify']);

    $frame_data = array ();
    $frame_data['title']   = $this->get_title ();
    $frame_data['content'] = $this->load_content ($data, true);
    $frame_data = array_merge ($frame_data, $this->load_components ());

    if ($return) return $this->load->view ($frame_path, $frame_data, $return);
    else $this->load->view ($frame_path, $frame_data, $return)->cache ($cache_time);
  }
  protected function output_error_json ($message, $code = 405, $cache = 0) {
    $server_protocol = (isset($_SERVER['SERVER_PROTOCOL'])) ? $_SERVER['SERVER_PROTOCOL'] : FALSE;
    if (substr (php_sapi_name (), 0, 3) == 'cgi')
      header ('Status: ' . $code . ' ' . $message, true);
    elseif (($server_protocol == 'HTTP/1.1') || ($server_protocol == 'HTTP/1.0'))
      header ($server_protocol . ' ' . $code . ' ' . $message, true, $code);
    else
      header ('HTTP/1.1 ' . $code . ' ' . $message, true, $code);

    return $this->output
                ->set_content_type ('application/json')
                ->set_output (json_encode (array (
                    'message' => $message
                  )))
                ->cache ($cache);
  }
}