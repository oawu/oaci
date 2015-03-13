<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2015 OA Wu Design
 */

class OrmImageUploader extends OrmUploader {
  private $configs = array ();

  public function __construct ($orm = null, $column_name = null) {
    parent::__construct ($orm, $column_name);

    $this->configs = Cfg::system ('orm_uploader', 'image_uploader');

    $this->CI->load->library ('image/ImageUtility');
  }
  // return sring
  protected function d4_url () {
    return $this->configs['d4_url'];
  }
  // return array
  public function getVersions () {
    return $this->configs['default_version'];
  }
  // return array
  public function path ($key = '') {
    if (($versions = ($versions = $this->getVersions ()) ? $versions : $this->configs['default_version']) && isset ($versions[$key]) && ($fileName = $key . $this->configs['separate_symbol'] . (string)$this))
      return parent::path ($fileName);
    else
      return array ();
    return $this->configs['debug'] ? error ('OrmUploader 錯誤！', '未知的 bucket，系統尚未支援' . $this->configs['bucket'] . ' 的空間！', '請檢查 config/system/orm_uploader.php 設定檔！') : array ();
  }








  // // return sring
  // public function getFileName () {
  //   return uniqid (rand () . '_');
  // }

  // // return array
  // // return string
  // public function url ($key = '') {
  //   if ($this->error)
  //     return $this->configs['debug'] ? call_user_func_array ('error', $this->error) : '';

  //   switch ($this->configs['bucket']) {
  //     case 'local':
  //       return ($path = $this->path ($key)) ? base_url ($path) : $this->d4_url ();
  //       break;
  //   }

  //   return $this->configs['debug'] ? error ('OrmUploader 錯誤！', '未知的 bucket，系統尚未支援' . $this->configs['bucket'] . ' 的空間！', '請檢查 config/system/orm_uploader.php 設定檔！') : '';
  // }

  // // return string
  // public function _createNewFiles ($fileInfo, $isUseMoveUploadedFile = false) {
  //   if ($this->error)
  //     return $this->configs['debug'] ? call_user_func_array ('error', $this->error) : '';

  //   if (!($versions = ($versions = $this->getVersions ()) ? $versions : $this->configs['default_version']))
  //     return $this->configs['debug'] ? error ('OrmUploader 錯誤！', 'Versions 格式錯誤，請檢查 getVersions () 或者 default_version！', '預設值 default_version 請檢查 config/system/orm_uploader.php 設定檔！') : '';

  //   switch ($this->configs['bucket']) {
  //     case 'local':
  //       if (!is_writable ($path = FCPATH . implode (DIRECTORY_SEPARATOR, $this->configs['base_directory']['local'])))
  //         return $this->configs['debug'] ? error ('OrmUploader 錯誤！', '資料夾不能儲存！路徑：' . $path, '請檢查 config/system/orm_uploader.php 設定檔！') : '';

  //       if (!file_exists (FCPATH . implode (DIRECTORY_SEPARATOR, $path = array_merge ($this->configs['base_directory']['local'], $this->getSavePath ())))) {
  //         $oldmask = umask (0);
  //         @mkdir (FCPATH . implode (DIRECTORY_SEPARATOR, $path), 0777, true);
  //         umask ($oldmask);
  //       }

  //       if (!is_writable (FCPATH . implode (DIRECTORY_SEPARATOR, $path)))
  //         return $this->configs['debug'] ? error ('OrmUploader 錯誤！', '資料夾不能儲存！路徑：' . $path, '請程式設計者確認狀況！') : '';

  //       $temp = FCPATH . implode (DIRECTORY_SEPARATOR, array_merge ($this->configs['temp_directory'], array ($this->configs['temp_file_name'])));

  //       if ($isUseMoveUploadedFile)
  //         @move_uploaded_file ($fileInfo['tmp_name'], $temp);
  //       else
  //         @rename ($fileInfo['tmp_name'], $temp);

  //       if (!is_readable ($temp))
  //         return $this->configs['debug'] ? error ('OrmUploader 錯誤！', '移動檔案錯誤！路徑：' . $temp, '請程式設計者確認狀況！') : '';

  //       $oldmask = umask (0);
  //       @chmod ($temp, 0777);
  //       umask ($oldmask);

  //       $result = true;

  //       try {
  //         $image = ImageUtility::create ($temp, null);
  //         $name = $this->getFileName () . ($this->configs['auto_add_format'] ? '.' . $image->getFormat () : '');

  //         foreach ($versions as $key => $version) {
  //           $new = FCPATH . implode (DIRECTORY_SEPARATOR, array_merge ($path, array ($key . $this->configs['separate_symbol'] . $name)));
  //           $result &= $this->_utility ($image, $new, $key, $version);
  //         }
  //       } catch (Exception $e) {
  //         return $this->configs['debug'] ? call_user_func_array ('error', $e->getMessages ()) : '';
  //       }

  //       return ($result &= @unlink ($temp)) ? $name : '';
  //       break;
  //   }
  //   return $this->configs['debug'] ? error ('OrmUploader 錯誤！', '未知的 bucket，系統尚未支援' . $this->configs['bucket'] . ' 的空間！', '請檢查 config/system/orm_uploader.php 設定檔！') : '';
  // }

  // // return boolean
  // private function _cleanOldFiles ($saveFileName) {
  //   if ($this->error)
  //     return $this->configs['debug'] ? call_user_func_array ('error', $this->error) : false;

  //   if (!(($versions = ($versions = $this->getVersions ()) ? $versions : $this->configs['default_version']) && ($versions = array_keys ($versions))))
  //     return $this->configs['debug'] ? error ('OrmUploader 錯誤！', 'Versions 格式錯誤，請檢查 getVersions () 或者 default_version！', '預設值 default_version 請檢查 config/system/orm_uploader.php 設定檔！') : false;

  //   switch ($this->configs['bucket']) {
  //     case 'local':
  //       $result = true;

  //       foreach ($versions as $version)
  //         if (file_exists ($path = FCPATH . implode (DIRECTORY_SEPARATOR, array_merge ($this->configs['base_directory']['local'], $this->getSavePath (), array ($version . $this->configs['separate_symbol'] . $this->column_value)))))
  //           $result &= @unlink ($path);

  //       if ($result) {
  //         $column_name = $this->column_name;
  //         $this->orm->$column_name = $saveFileName;
  //         $this->orm->save ();
  //         $this->column_value = $saveFileName;
  //         $this->orm->$column_name = $this;
  //       }
  //       return $result;
  //       break;
  //   }

  //   return $this->configs['debug'] ? error ('OrmUploader 錯誤！', '未知的 bucket，系統尚未支援' . $this->configs['bucket'] . ' 的空間！', '請檢查 config/system/orm_uploader.php 設定檔！') : false;
  // }

  // // return boolean
  // public function put ($fileInfo, $isUseMoveUploadedFile = true) {
  //   if ($this->error)
  //     return $this->configs['debug'] ? call_user_func_array ('error', $this->error) : false;

  //   if (is_array ($fileInfo))
  //     foreach (array ('name', 'type', 'tmp_name', 'error', 'size') as $key)
  //       if (!isset ($fileInfo[$key]))
  //         return false;
  //       else ;
  //   else if (is_string ($fileInfo) && is_writable ($fileInfo))
  //     $fileInfo = array ('name' => 'file', 'type' => '', 'tmp_name' => $fileInfo, 'error' => '', 'size' => '1');
  //   else
  //     return false;

  //   return ($saveFileName = $this->_createNewFiles ($fileInfo, $isUseMoveUploadedFile)) && $this->_cleanOldFiles ($saveFileName);
  // }

  // // return boolean
  // public function cleanAllFiles ($isAutoSave = true) {
  //   if ($this->error)
  //     return $this->configs['debug'] ? call_user_func_array ('error', $this->error) : false;

  //   switch ($this->configs['bucket']) {
  //     case 'local':
  //       return $this->_cleanOldFiles ('');
  //       break;
  //   }

  //   return $this->configs['debug'] ? error ('OrmUploader 錯誤！', '未知的 bucket，系統尚未支援' . $this->configs['bucket'] . ' 的空間！', '請檢查 config/system/orm_uploader.php 設定檔！') : false;
  // }

  // // return boolean
  // public function put_url ($url) {
  //   if ($this->error)
  //     return $this->configs['debug'] ? call_user_func_array ('error', $this->error) : false;

  //   $temp = FCPATH . implode (DIRECTORY_SEPARATOR, array_merge ($this->configs['temp_directory'], array ($this->configs['temp_file_name'])));

  //   if (($temp = download_web_file ($url, $temp)) && $this->put ($temp, false))
  //     return file_exists ($temp) ? @unlink ($temp) : true;
  //   else
  //     return false;

  //   return $this->configs['debug'] ? error ('OrmUploader 錯誤！', '未知的 bucket，系統尚未支援' . $this->configs['bucket'] . ' 的空間！', '請檢查 config/system/orm_uploader.php 設定檔！') : false;
  // }

  // // return boolean
  // private function _utility ($image, $save, $key, $version) {
  //   if ($version)
  //     if (is_callable (array ($image, $method = array_shift ($version))))
  //       call_user_func_array (array ($image, $method), $version);
  //     else
  //       return $this->configs['debug'] ? error ('OrmUploader 錯誤！', 'ImageUtility 無法呼叫的 method，method：' . $method, '請程式設計者確認狀況！') : '';
  //   return $image->save ($save, true);
  // }

  // // return array
  // public function save_as ($key, $version) {
  //   if ($this->error)
  //     return $this->configs['debug'] ? call_user_func_array ('error', $this->error) : array ();

  //   if (!($key && $version))
  //     return $this->configs['debug'] ? error ('OrmUploader 錯誤！', '參數錯誤，請檢查 save_as 函式參數！', '請程式設計者確認狀況！') : array ();

  //   if (!(($versions = ($versions = $this->getVersions ()) ? $versions : $this->configs['default_version'])))
  //     return $this->configs['debug'] ? error ('OrmUploader 錯誤！', 'Versions 格式錯誤，請檢查 getVersions () 或者 default_version！', '預設值 default_version 請檢查 config/system/orm_uploader.php 設定檔！') : array ();

  //   switch ($this->configs['bucket']) {
  //     case 'local':
  //       if (in_array ($key, array_keys ($versions)))
  //         return is_readable (FCPATH . implode (DIRECTORY_SEPARATOR, $ori_path = array_merge ($this->configs['base_directory'][$this->configs['bucket']], $this->getSavePath (), array ($key . $this->configs['separate_symbol'] . (string)$this)))) ? $ori_path : '';

  //       foreach ($versions as $ori_key => $ori_version)
  //         if (is_readable (FCPATH . implode (DIRECTORY_SEPARATOR, $ori_path = array_merge ($this->configs['base_directory'][$this->configs['bucket']], $this->getSavePath (), array ($ori_key . $this->configs['separate_symbol'] . ($name = (string)$this))))))
  //           break;

  //       if (!$ori_path)
  //         return $this->configs['debug'] ? error ('OrmUploader 錯誤！', '沒有任何的檔案可以被使用！', '請確認 getVersions () 函式內有存在的檔案可被另存！', '請程式設計者確認狀況！') : array ();

  //       if (!file_exists (FCPATH . implode (DIRECTORY_SEPARATOR, ($path = array_merge ($this->configs['base_directory'][$this->configs['bucket']], $this->getSavePath ()))))) {
  //         $oldmask = umask (0);
  //         @mkdir (FCPATH . implode (DIRECTORY_SEPARATOR, $path), 0777, true);
  //         umask ($oldmask);
  //       }

  //       if (!is_writable (FCPATH . implode (DIRECTORY_SEPARATOR, $path)))
  //         return $this->configs['debug'] ? error ('OrmUploader 錯誤！', '資料夾不能儲存！路徑：' . $path, '請程式設計者確認狀況！') : '';

  //       try {
  //         $image = ImageUtility::create (FCPATH . implode (DIRECTORY_SEPARATOR, $ori_path), null);
  //         $path = array_merge ($path, array ($key . $this->configs['separate_symbol'] . $name));

  //         if ($this->_utility ($image, FCPATH . implode (DIRECTORY_SEPARATOR, $path), $key, $version))
  //           return $path;
  //         else
  //           return array ();
  //       } catch (Exception $e) {
  //         return $this->configs['debug'] ? call_user_func_array ('error', $e->getMessages ()) : '';
  //       }
  //       break;
  //   }

  //   return $this->configs['debug'] ? error ('OrmUploader 錯誤！', '未知的 bucket，系統尚未支援' . $this->configs['bucket'] . ' 的空間！', '請檢查 config/system/orm_uploader.php 設定檔！') : '';
  // }

}
