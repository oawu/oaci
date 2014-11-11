<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2014 OA Wu Design
 */
class ImageUtility {
  private $CI = null;
  private $object = null;

  public function __construct ($fileName = null, $module = null, $options = array ()) {
    if ($fileName === null)
      return ;

    if (!is_readable ($fileName))
      return show_error ("The image file : " . $fileName . " not found or not readable!<br/>Please confirm your program again.");

    $this->CI =& get_instance ();
    $this->CI->load->helper ('oa');
    $this->CI->load->library ("cfg");

    if (!(($module = $module ? strtolower ($module) : Cfg::system ('image_utility', 'module')) && in_array ($module, array_keys ($modules = Cfg::system ('image_utility', 'modules')))))
      return show_error ("The file name or module select error, please confirm your program again.");
    
    if (!is_readable ($path = utilitySameLevelPath (FCPATH . APPPATH . DIRECTORY_SEPARATOR . implode (DIRECTORY_SEPARATOR, $this->CI->get_libraries_path ()) . DIRECTORY_SEPARATOR . $modules[$module] . EXT)))
      return show_error ("The image utility class array format is error!<br/>It must be 'gd' or 'imgk'!<br/>Please confirm your program again.");
    
    if (!class_exists ($modules[$module]))
      require_once $path;
    
    $this->object = new $modules[$module] ($fileName, $options);
  }

  public static function create ($fileName, $module = null, $options = array ()) {
    $uti = new ImageUtility ($fileName, $module = null, $options);
    return $uti->getObject () ? $uti->getObject () : show_error ("Init ImageUtility object error!<br/>Please confirm your program again.");
  }

  public static function make_block9 () {
    if (count ($params = func_get_args ()) < 1)
      return false;
    if (!(($module = Cfg::system ('image_utility', 'module')) && in_array ($module, array_keys ($modules = Cfg::system ('image_utility', 'modules')))))
      return show_error ("The file name or module select error, please confirm your program again.");
    
    $CI =& get_instance ();
    if (!is_readable ($path = utilitySameLevelPath (FCPATH . APPPATH . DIRECTORY_SEPARATOR . implode (DIRECTORY_SEPARATOR, $CI->get_libraries_path ()) . DIRECTORY_SEPARATOR . $modules[$module] . EXT)))
      return show_error ("The image utility class array format is error!<br/>It must be 'gd' or 'imgk'!<br/>Please confirm your program again.");
    
    if (!class_exists ($modules[$module]))
      require_once $path;
    return call_user_func_array (array ($modules[$module], 'make_block9'), $params);
  }
  public function getObject () { return $this->object; }
}
