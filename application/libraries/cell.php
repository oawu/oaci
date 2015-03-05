<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2015 OA Wu Design
 */
class Cell {
  private $CI = null;
  private $configs = array ();

  public function __construct ($configs = array ()) {
    $this->CI =& get_instance ();
    $this->CI->load->library ("cfg");
    $this->configs = array_merge (Cfg::system ('cell'), $configs);

    if ($this->configs['driver'] == 'redis') {
      $this->CI->load->library ("redis");
      $this->configs['driver'] = $this->CI->redis->getStatus ($error) ? 'redis' : 'file';
    }
  }

  public function render_cell ($class, $method, $params = array ()) {
    if (!preg_match ('/(' . $this->configs['class_suffix'] . ')$/', $class))
      return show_error ("The class name doesn't have suffix!<br/>class name: " . $class . "<br/>suffix: " . Cfg::system ('cell', 'class_suffix'));

    if (!is_readable ($path = FCPATH . APPPATH . implode (DIRECTORY_SEPARATOR, array_merge ($this->configs['folders']['controller'], array ($class . EXT)))))
      return show_error ("The Cell's controllers is not exist or can't read!<br/>File: " . $path);

    include_once ($path);
    $Class = ucfirst ($class);
    $object = new $Class ();

    if (!is_callable (array ($object, $method)))
      return show_error ("The class: " . $path . " not exist method: " . $method);

    if ($this->configs['is_enabled'] && is_callable (array ($object, $cache_method = $this->configs['method_prefix'] . $method)) && ($option = call_user_func_array (array ($object, $cache_method), $params))) {
      $option['time'] = isset ($option['time']) && $option['time'] > 0 ? $option['time'] : $this->configs['d4_cache_time'];
      $option['key'] = isset ($option['key']) && $option['key'] ? $option['key'] : null;
      $name = array_filter (is_array ($option['key']) ? array_merge (array ($this->configs['redis_main_key'], $class, $method), $option['key']) : array ($this->configs['redis_main_key'], $class, $method, $option['key']));

      if ($this->configs['driver'] == 'redis') {
        if (($value = $this->CI->redis->hGetArray ($key = implode (':', $name))) && time () < $value['time'])
          $view = $value['data'];
        else
          $this->CI->redis->hmset ($key, 'data', $view = call_user_func_array (array ($object, $method), $params), 'time', time () + $option['time']);
      } else {
        echo "string";
      }
    } else {
      $view = call_user_func_array (array ($object, $method), $params);
    }

    return $view;
  }

  public function clean_cell ($keys) {
    if ($this->configs['driver'] == 'redis') {
      array_unshift ($keys, $this->configs['redis_main_key']);

      $keys = implode (':', $keys);
      $keys = !preg_match ('/\*$/', $keys) ? $this->CI->redis->exists ($keys) ? array ($keys) : array () : $this->CI->redis->keys ($keys);

      if ($keys)
        foreach ($keys as $key)
          $this->CI->redis->del ($keys);
    } else {
      echo "string";
    }
  }
}

/*
class Cell {
  private $CI = null;
  private $is_enabled = null;
  private $controller_folder = null;
  private $cache_folder = null;

  public function __construct ($configs = array ()) {
    $this->CI =& get_instance ();

    $this->CI->load->helper ('oa');
    $this->CI->load->library ("cfg");
    $this->CI->load->driver ('cache', array ('adapter' => 'apc', 'backup' => 'file'));

    $this->is_enabled = Cfg::system ('cell', 'is_enabled');
    $this->controller_folder = utilitySameLevelPath (FCPATH . Cfg::system ('cell', 'controller_folder'));
    $this->cache_folder = utilitySameLevelPath (Cfg::system ('cell', 'cache_folder'));

    if (!is_writable ($temp_path = utilitySameLevelPath (FCPATH . DIRECTORY_SEPARATOR . $this->cache_folder . DIRECTORY_SEPARATOR))) {
      $oldmask = umask (0);
      @mkdir ($temp_path, 0777, true);
      umask ($oldmask);
    }

    $this->cache_prefix = strlen ($cache_prefix = Cfg::system ('cell', 'method_prefix')) ? $cache_prefix : null;
  }

  public function render_cell ($class, $method, $params = array ()) {
    if (preg_match_all ('/(' . Cfg::system ('cell', 'class_suffix') . ')$/', $class) == 1) {
      if (is_readable ($file = utilitySameLevelPath ($this->controller_folder . DIRECTORY_SEPARATOR . ($class = strtolower ($class)) . EXT))) {
        require_once ($file);
        $class = ucfirst ($class);
        $object = new $class ();

        if (is_callable (array ($object, $method))) {
          $option = array ();

          if ($this->is_enabled && ($this->cache_prefix !== null) && is_callable (array ($object, $cache_method = $this->cache_prefix . $method)) && ($option = call_user_func_array (array ($object, $cache_method), $params)) &&  isset ($option['time'])) {
            if (!($option['time'] >= 1)) $option['time'] = Cfg::system ('cell', 'd4_time');

            if (isset ($option['key']) && count ($option['key'] = array_filter (explode ('/', $option['key'])))) {
              $option['path'] = utilitySameLevelPath ($this->cache_folder . DIRECTORY_SEPARATOR . strtolower ($class) . '_|_' . strtolower ($method) . DIRECTORY_SEPARATOR);

              if (count ($option['key']) == 1) {
                $option['key'] = $option['key'][0];
              } else {
                $key = array_pop ($option['key']);
                $option['path'] = utilitySameLevelPath ($option['path'] . DIRECTORY_SEPARATOR . implode (DIRECTORY_SEPARATOR, $option['key']) . DIRECTORY_SEPARATOR);
                $option['key'] = $key;
              }

              $option['key'] = Cfg::system ('cell', 'file_prefix') . '_|_' . $option['key'];
            } else {
              $option['path'] = $this->cache_folder;
              $option['key'] = Cfg::system ('cell', 'file_prefix') . '_|_' . strtolower ($class) . '_|_' . strtolower ($method);
            }
            if (!is_writable ($temp_path = utilitySameLevelPath (FCPATH . DIRECTORY_SEPARATOR . $option['path']))) {
              $oldmask = umask (0);
              @mkdir ($temp_path, 0777, true);
              umask ($oldmask);
            }
          }

          if (!count ($option) || (($view = $this->CI->cache->file->get ($option['key'], $option['path'])) === false)) {
            $view = call_user_func_array (array ($object, $method), $params);
            if (count ($option))
            $res = $this->CI->cache->file->save ($option['key'], $view, $option['time'], $option['path']);
          }
          return $view;
        } else show_error ("The class: " . $file . " not exist method: " . $method);
      } else { show_error ("The Cell's controllers is not exist or can't read! File: " . $file); }
    } else { show_error ("The class name doesn't have suffix, class name: " . $class . ", suffix: " . Cfg::system ('cell', 'class_suffix')); }
  }

  public function clear_cell ($class, $method, $key = null) {
    if (isset ($key) && count ($key = array_filter (explode ('/', $key)))) {
      $path = utilitySameLevelPath ($this->cache_folder . DIRECTORY_SEPARATOR . strtolower ($class) . '_|_' . strtolower ($method) . DIRECTORY_SEPARATOR);

      if (count ($key) == 1) {
        $key = $key[0];
      } else {
        $temp_key = array_pop ($key);
        $path = utilitySameLevelPath ($path . DIRECTORY_SEPARATOR . implode (DIRECTORY_SEPARATOR, $key) . DIRECTORY_SEPARATOR);
        $key = $temp_key;
      }

      $key = Cfg::system ('cell', 'file_prefix') . '_|_' . $key;
    } else {
      $path = $this->cache_folder;
      $key = Cfg::system ('cell', 'file_prefix') . '_|_' . strtolower ($class) . '_|_' . strtolower ($method);
    }

    if (!is_writable ($temp_path = utilitySameLevelPath (FCPATH . DIRECTORY_SEPARATOR . $path))) {
      $oldmask = umask (0);
      @mkdir ($temp_path, 0777, true);
      umask ($oldmask);
    }

    @$this->CI->cache->file->delete ($key, $path);
  }

  public function clean_cell ($class = null, $method = null, $key = null) {
    if (!isset ($class) && !isset ($method) && !isset ($key)) {
      @$this->CI->cache->file->clean ($this->cache_folder, true);
    } else if (isset ($class) && isset ($method)) {
      $path = utilitySameLevelPath ($this->cache_folder . DIRECTORY_SEPARATOR . strtolower ($class) . '_|_' . strtolower ($method) . DIRECTORY_SEPARATOR);
      if (isset ($key) && (count ($key = array_filter (explode ('/', $key))) > 1)) {
        array_pop ($key);
        $path = utilitySameLevelPath ($path . DIRECTORY_SEPARATOR . implode (DIRECTORY_SEPARATOR, $key) . DIRECTORY_SEPARATOR);
      }
      if (!is_writable ($temp_path = utilitySameLevelPath (FCPATH . DIRECTORY_SEPARATOR . $path))) {
        $oldmask = umask (0);
        @mkdir ($temp_path, 0777, true);
        umask ($oldmask);
      } else {
        @$this->CI->cache->file->clean ($path, true);
      }
    }
  }
}

*/
class Cell_Controller {
  private $CI = null;
  private $view_folder = null;

  public function __construct () {
    $this->CI =& get_instance ();
    $this->view_folder = utilitySameLevelPath (FCPATH . Cfg::system ('cell', 'view_folder'));
  }

  public function get_CI () {
    return $this->CI;
  }

  protected function load_view ($data = array (), $set_method = null, $set_class = null) {
    $trace = debug_backtrace (DEBUG_BACKTRACE_PROVIDE_OBJECT);
    if (isset ($trace) && count ($trace) > 1 && isset ($trace[1]) && isset ($trace[1]['class']) && isset ($trace[1]['function']) && is_string ($class = strtolower ($trace[1]['class'])) && is_string ($method = strtolower ($trace[1]['function'])) && strlen ($class) && strlen ($method)) {
      if (is_readable ($_ci_path = utilitySameLevelPath ($this->view_folder . DIRECTORY_SEPARATOR . ($set_class ? $set_class : $class) . DIRECTORY_SEPARATOR . ($set_method ? $set_method : $method) . EXT))) {
        extract ($data);
        ob_start();

        if (((bool)@ini_get('short_open_tag') === FALSE) && (config_item('rewrite_short_tags') == TRUE)) echo eval('?>'.preg_replace("/;*\s*\?>/", "; ?>", str_replace('<?=', '<?php echo ', file_get_contents($_ci_path))));
        else include ($_ci_path);

        $buffer = ob_get_contents ();
        @ob_end_clean ();
        return $buffer;
      } else { show_error ("The Cell's controllers is not exist or can't read! File: " . $_ci_path); }
    } else { show_error ('The debug_backtrace Error!'); }
  }
}