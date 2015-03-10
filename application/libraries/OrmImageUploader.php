<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2015 OA Wu Design
 */

class OrmImageUploader {


  public static function bind ($column_name, $instance_class_name = null) {
    if (($trace = debug_backtrace (DEBUG_BACKTRACE_PROVIDE_OBJECT)))
      show_error ();


    if (($trace = debug_backtrace (DEBUG_BACKTRACE_PROVIDE_OBJECT)) && (count ($trace) > 1) && isset ($trace[1]) && isset ($trace[1]['object']) && is_object ($orm = $trace[1]['object']) && $column_name && strlen ($column_name)) {

      $CI =& get_instance ();
      $CI->load->helper ('oa');

      if (!$instance_class_name)
        $instance_class_name = get_class ($orm) . Cfg::system ('model', 'uploader', 'instances', 'class_suffix');

      if (is_readable ($path = utilitySameLevelPath (Cfg::system ('model', 'uploader', 'instances', 'directory') . DIRECTORY_SEPARATOR . $instance_class_name . EXT)))
        require_once $path;
      else
        $instance_class_name = get_called_class ();

      $object = new $instance_class_name ($orm, $column_name);
    } else {
      show_error ("The create ModelUploader object happen unknown error...<br/>Please confirm your program again.");
    }
  }
}







// class OrmImageUploader {
//   private $CI = null;
//   private $orm = null;
//   private $column_name = null;
//   private $column_value = null;

//   public function __construct ($orm = null, $column_name = null) {
//     if ($orm && $column_name && $orm->attributes () && in_array ($column_name, array_keys ($orm->attributes ()))) {
//       $this->CI =& get_instance ();
//       $this->CI->load->helper ('oa');
//       $this->CI->load->helper ('file');
//       $this->CI->load->library ("cfg");

//       $this->orm = $orm;
//       $this->column_name = $column_name;
//       $this->column_value = $orm->$column_name;
//       $orm->$column_name = $this;
//       $this->error = null;
//     }
//   }

//   public function getColumnValue ($column_name) {
//     return isset ($this->orm->$column_name) ? $this->orm->$column_name : '';
//   }

//   public function getTableName () {
//     return $this->orm->table ()->table;
//   }

//   public function getColumnName () {
//     return $this->column_name;
//   }

//   public function getSavePath () {
//     return ($id = $this->getColumnValue ('id')) ? $this->getTableName () . DIRECTORY_SEPARATOR . $this->getColumnName () . DIRECTORY_SEPARATOR .floor ($id / 1000000) . DIRECTORY_SEPARATOR . floor (($id % 1000000) / 10000) . DIRECTORY_SEPARATOR . floor ((($id % 1000000) % 10000) / 100) . DIRECTORY_SEPARATOR . ($id % 100) . DIRECTORY_SEPARATOR : ($this->getTableName () . DIRECTORY_SEPARATOR . $this->getColumnName () . DIRECTORY_SEPARATOR);
//   }

//   public function getVersions () {
//     return Cfg::system ('model', 'uploader', 'default_version');
//   }

//   public function getFileName () {
//     return uniqid (rand () . '_');
//   }

//   public function d4_url () {
//     return '';
//   }

//   public function put_url ($url) {
//     return ($fileName = download_web_file ($url, utilitySameLevelPath (Cfg::system ('model', 'uploader', 'temp_directory') . DIRECTORY_SEPARATOR . Cfg::system ('model', 'uploader', 'temp_file_name')))) && $this->put ($fileName, false) ? file_exists ($fileName) ? @unlink ($fileName) : true : false;
//   }

//   public function save_as ($key, $version) {
//     if (Cfg::system ('model', 'uploader', 'bucket', 'type') == 'local') {
//       if (!(is_string ($key) && is_array ($version)))
//         show_error ("The key and version format error!<br/>Please confirm your program again.");

//       if (!($versions = $this->getVersions ()) && !($versions = Cfg::system ('model', 'uploader', 'default_version')))
//         show_error ("The versions format error!<br/>Please confirm your program again.");

//       if (in_array ($key, array_keys ($versions)))
//         return is_readable ($path = utilitySameLevelPath (FCPATH . DIRECTORY_SEPARATOR .Cfg::system ('model', 'uploader', 'bucket', 'local', 'base_directory') . DIRECTORY_SEPARATOR . $this->getSavePath () . DIRECTORY_SEPARATOR . $key . Cfg::system ('model', 'uploader', 'file_name', 'separate_symbol') . (string)$this)) ? $path : '';

//       foreach ($versions as $ori_key => $ori_version)
//         if (!($path = '') && is_readable ($path = utilitySameLevelPath (FCPATH . DIRECTORY_SEPARATOR .Cfg::system ('model', 'uploader', 'bucket', 'local', 'base_directory') . DIRECTORY_SEPARATOR . $this->getSavePath () . DIRECTORY_SEPARATOR . $ori_key . Cfg::system ('model', 'uploader', 'file_name', 'separate_symbol') . ($fileName = (string)$this))))
//           break;

//       if (!$path)
//         show_error ("There is not file can be used!<br/>Please confirm your program again.");

//       $separate_symbol = Cfg::system ('model', 'uploader', 'file_name', 'separate_symbol');

//       $this->CI->load->library ('ImageUtility');
//       $image = ImageUtility::create ($path, null, array ('resizeUp' => false));

//       try {
//         if (!is_writable ($path = utilitySameLevelPath (FCPATH . DIRECTORY_SEPARATOR . Cfg::system ('model', 'uploader', 'bucket', 'local', 'base_directory') . DIRECTORY_SEPARATOR)))
//           show_error ("The save base directory can not be 'write'!<br/>Directory : " . $path . "<br/>Please confirm your program again.");

//         if (!file_exists ($path = utilitySameLevelPath ($path . DIRECTORY_SEPARATOR . $this->getSavePath () . DIRECTORY_SEPARATOR))) {
//           $oldmask = umask (0);
//           @mkdir ($path, 0777, true);
//           umask ($oldmask);
//         } else if (!is_writable ($path)) {
//           show_error ("The save base directory can not be 'write'!<br/>Directory : " . $path . "<br/>Please confirm your program again.");
//         }

//         if (is_callable (array ($image, $method = array_shift ($version))))
//           call_user_func_array (array ($image, $method), $version);

//         $path = utilitySameLevelPath ($path . DIRECTORY_SEPARATOR . $key . $separate_symbol . $fileName);
//         if ($image->save ($path, true))
//           return $path;
//       } catch (Exception $e) {
//         return '';
//       }
//       return '';
//     }
//   }

//   public function put ($fileInfo, $isUseMoveUploadedFile = true) {
//     if (is_array ($fileInfo))
//       foreach (array ('name', 'type', 'tmp_name', 'error', 'size') as $key)
//         if (!isset ($fileInfo[$key]))
//           return false;
//         else ;
//     else if (is_string ($fileInfo) && is_writable ($fileInfo))
//       $fileInfo = array ('name' => 'file', 'type' => '', 'tmp_name' => $fileInfo, 'error' => '', 'size' => '1');
//     else
//       return false;

//     return ($saveFileName = $this->_createNewFiles ($fileInfo, $isUseMoveUploadedFile)) && $this->_cleanOldFiles ($saveFileName);
//   }

//   private function _cleanOldFiles ($saveFileName) {
//     if (Cfg::system ('model', 'uploader', 'bucket', 'type') == 'local') {
//       if (!(($versions = $this->getVersions ()) || ($versions = Cfg::system ('model', 'uploader', 'default_version'))) || !($versions = array_keys ($versions)))
//         return false;
//       $separate_symbol = Cfg::system ('model', 'uploader', 'file_name', 'separate_symbol');
//       $directory = FCPATH . DIRECTORY_SEPARATOR . Cfg::system ('model', 'uploader', 'bucket', 'local', 'base_directory') . DIRECTORY_SEPARATOR . $this->getSavePath () . DIRECTORY_SEPARATOR;

//       $result = true;
//       foreach ($versions as $version)
//         if (file_exists ($path = utilitySameLevelPath ($directory . DIRECTORY_SEPARATOR . $version . $separate_symbol . $this->column_value)))
//           $result &= @unlink ($path);

//       if ($result) {
//         $column_name = $this->column_name;
//         $this->orm->$column_name = $saveFileName;
//         $this->orm->save ();
//         $this->column_value = $saveFileName;
//         $this->orm->$column_name = $this;
//         return $result;
//       }
//     }
//     return false;
//   }

//   public function _createNewFiles ($fileInfo, $isUseMoveUploadedFile = false) {
//     $result = '';

//     if (Cfg::system ('model', 'uploader', 'bucket', 'type') == 'local') {
//       if (!($versions = $this->getVersions ()) && !($versions = Cfg::system ('model', 'uploader', 'default_version')))
//         show_error ("The versions format error!<br/>Please confirm your program again.");

//       if (!is_writable ($path = utilitySameLevelPath (FCPATH . DIRECTORY_SEPARATOR . Cfg::system ('model', 'uploader', 'bucket', 'local', 'base_directory') . DIRECTORY_SEPARATOR)))
//         show_error ("The save base directory can not be 'write'!<br/>Directory : " . $path . "<br/>Please confirm your program again.");

//       if (!file_exists ($path = utilitySameLevelPath ($path . DIRECTORY_SEPARATOR . $this->getSavePath () . DIRECTORY_SEPARATOR))) {
//         $oldmask = umask (0);
//         @mkdir ($path, 0777, true);
//         umask ($oldmask);
//       } else if (!is_writable ($path)) {
//         show_error ("The save base directory can not be 'write'!<br/>Directory : " . $path . "<br/>Please confirm your program again.");
//       }

//       $fileName = $this->getFileName ();
//       $separate_symbol = Cfg::system ('model', 'uploader', 'file_name', 'separate_symbol');
//       $auto_add_format = Cfg::system ('model', 'uploader', 'file_name', 'auto_add_format');

//       $tempFileName = utilitySameLevelPath (Cfg::system ('model', 'uploader', 'temp_directory') . DIRECTORY_SEPARATOR . Cfg::system ('model', 'uploader', 'temp_file_name'));

//       if (($isUseMoveUploadedFile && @move_uploaded_file ($fileInfo['tmp_name'], $tempFileName)) || @rename ($fileInfo['tmp_name'], $tempFileName)) {
//         $oldmask = umask (0);
//         @chmod ($tempFileName, 0777);
//         umask ($oldmask);

//         $this->CI->load->library ('ImageUtility');

//         $result = '1';
//         foreach ($versions as $version_key => $version_format) {
//           $image = ImageUtility::create ($tempFileName, null, array ('resizeUp' => false));

//           try {
//             $saveFileName = $fileName . ($auto_add_format ? '.' . $image->getFormat () : '');

//             if ($version_format)
//               if (is_callable (array ($image, $method = array_shift ($version_format))))
//                 call_user_func_array (array ($image, $method), $version_format);
//               else
//                 show_error ("There is a method can not be call, That name is '" . (isset ($method) ? $method : "null") . "'.<br/>Please confirm your program again.");

//             $newFileName = utilitySameLevelPath ($path . DIRECTORY_SEPARATOR . $version_key . $separate_symbol . $saveFileName);
//             $result &= $image->save ($newFileName, true);
//           } catch (Exception $e) {
//             $result &= '';
//           }
//         }
//         $result = ($result &= @unlink ($tempFileName)) ? $saveFileName : $result;
//       }
//     }
//     return $result;
//   }

//   public function cleanAllFiles ($isAutoSave = true) {
//     if (Cfg::system ('model', 'uploader', 'bucket', 'type') == 'local') {
//       if (!file_exists ($path = utilitySameLevelPath (FCPATH . DIRECTORY_SEPARATOR . Cfg::system ('model', 'uploader', 'bucket', 'local', 'base_directory') . DIRECTORY_SEPARATOR . $this->getSavePath () . DIRECTORY_SEPARATOR)))
//         return true;

//       if (!is_writable ($path))
//         show_error ("The save base directory can not be 'write'!<br/>Directory : " . $path . "<br/>Please confirm your program again.");

//       if (delete_files ($path, true)) {
//         if ($isAutoSave) {
//           $column_name = $this->column_name;
//           $this->orm->$column_name = '';
//           $this->orm->save ();
//         }
//         return true;
//       }
//     }
//     return false;
//   }

//   public function path ($key = '') {
//     if (Cfg::system ('model', 'uploader', 'bucket', 'type') == 'local') {
//       return ($fileName = (string)$this) && (($versions = $this->getVersions ()) || ($versions = Cfg::system ('model', 'uploader', 'default_version'))) && isset ($versions[$key]) && is_readable (utilitySameLevelPath (FCPATH . DIRECTORY_SEPARATOR . ($path = utilitySameLevelPath (Cfg::system ('model', 'uploader', 'bucket', 'local', 'base_directory') . DIRECTORY_SEPARATOR . $this->getSavePath () . DIRECTORY_SEPARATOR . $key . Cfg::system ('model', 'uploader', 'file_name', 'separate_symbol') . $fileName)))) ? $path : '';
//     } else {
//       return '';
//     }
//   }

//   public function url ($key = '') {
//     if (Cfg::system ('model', 'uploader', 'bucket', 'type') == 'local') {
//       return ($path = $this->path ($key)) ? base_url ($path) : $this->d4_url ();
//     } else {
//       return $this->d4_url ();
//     }
//   }

//   public function __toString () {
//     return (string)$this->column_value;
//   }

//   public static function bind ($column_name, $instance_class_name = null) {
//     if (($trace = debug_backtrace (DEBUG_BACKTRACE_PROVIDE_OBJECT)) && (count ($trace) > 1) && isset ($trace[1]) && isset ($trace[1]['object']) && is_object ($orm = $trace[1]['object']) && $column_name && strlen ($column_name)) {

//       $CI =& get_instance ();
//       $CI->load->helper ('oa');

//       if (!$instance_class_name)
//         $instance_class_name = get_class ($orm) . Cfg::system ('model', 'uploader', 'instances', 'class_suffix');

//       if (is_readable ($path = utilitySameLevelPath (Cfg::system ('model', 'uploader', 'instances', 'directory') . DIRECTORY_SEPARATOR . $instance_class_name . EXT)))
//         require_once $path;
//       else
//         $instance_class_name = get_called_class ();

//       $object = new $instance_class_name ($orm, $column_name);
//     } else {
//       show_error ("The create ModelUploader object happen unknown error...<br/>Please confirm your program again.");
//     }
//   }
// }