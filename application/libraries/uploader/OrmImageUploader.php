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
  protected function d4Url () {
    return $this->configs['d4_url'];
  }
  // return array
  protected function getVersions () {
    return $this->configs['default_version'];
  }
  // return array
  public function path ($key = '') {
    if (($versions = ($versions = $this->getVersions ()) ? $versions : $this->configs['default_version']) && isset ($versions[$key]) && ($fileName = $key . $this->configs['separate_symbol'] . (string)$this))
      return parent::path ($fileName);
    else
      return array ();
    return $this->configs['debug'] ? error ('OrmUploader 錯誤！', '未知的 bucket，系統尚未支援' . $this->getBucket () . ' 的空間！', '請檢查 config/system/orm_uploader.php 設定檔！') : array ();
  }
  // return boolean
  private function _utility ($image, $save, $key, $version) {
    if ($version)
      if (is_callable (array ($image, $method = array_shift ($version))))
        call_user_func_array (array ($image, $method), $version);
      else
        return $this->configs['debug'] ? error ('OrmUploader 錯誤！', 'ImageUtility 無法呼叫的 method，method：' . $method, '請程式設計者確認狀況！') : '';
    return $image->save ($save, true);
  }
  // return array
  public function getAllPaths () {
    if ($this->error)
      return $this->configs['debug'] ? call_user_func_array ('error', $this->error) : array ();

    if (!($versions = ($versions = $this->getVersions ()) ? $versions : $this->configs['default_version']))
      return $this->configs['debug'] ? error ('OrmUploader 錯誤！', 'Versions 格式錯誤，請檢查 getVersions () 或者 default_version！', '預設值 default_version 請檢查 config/system/orm_uploader.php 設定檔！') : '';

    $paths = array ();
    foreach ($versions as $key => $version)
      if (is_writable (implode (DIRECTORY_SEPARATOR, $path = array_merge ($this->getBaseDirectory (), $this->getSavePath (), array ($key . $this->configs['separate_symbol'] . (string)$this)))))
        array_push ($paths, $path);
    return $paths;
  }
  // return boolean
  protected function moveFileAndUploadColumn ($temp, $save_path, $ori_name) {
    if ($this->error)
      return $this->configs['debug'] ? call_user_func_array ('error', $this->error) : '';

    if (!($versions = ($versions = $this->getVersions ()) ? $versions : $this->configs['default_version']))
      return $this->configs['debug'] ? error ('OrmUploader 錯誤！', 'Versions 格式錯誤，請檢查 getVersions () 或者 default_version！', '預設值 default_version 請檢查 config/system/orm_uploader.php 設定檔！') : '';

    if (!is_writable (FCPATH . implode (DIRECTORY_SEPARATOR, $path = $this->configs['temp_directory'])))
      return $this->configs['debug'] ? error ('OrmUploader 錯誤！', '暫存資料夾不可讀寫或不存在！', '請檢查暫存資料夾是否存在以及可讀寫！', '預設值 暫存資料夾 請檢查 config/system/orm_uploader.php 設定檔！') : false;

    $news = array ();
    try {
      $image = ImageUtility::create ($temp, null);
      $name = $this->getRandomName () . ($this->configs['auto_add_format'] ? '.' . $image->getFormat () : '');

      foreach ($versions as $key => $version) {
        $new_name = $key . $this->configs['separate_symbol'] . $name;
        $new_path = FCPATH . implode (DIRECTORY_SEPARATOR, array_merge ($path, array ($new_name)));
        array_push ($news, array ('name' => $new_name, 'path' => $new_path));

        if (!$this->_utility ($image, $new_path, $key, $version))
          return $this->configs['debug'] ? error ('OrmUploader 錯誤！', '圖想處理失敗！', '請程式設計者確認狀況！') : false;
      }
    } catch (Exception $e) {
      return $this->configs['debug'] ? call_user_func_array ('error', $e->getMessages ()) : '';
    }

    if (count ($news) != count ($versions))
      return $this->configs['debug'] ? error ('OrmUploader 錯誤！', '不明原因錯誤！', '請程式設計者確認狀況！') : false;


    switch ($this->getBucket ()) {
      case 'local':
        foreach ($news as $new)
          if (!@rename ($new['path'], FCPATH . implode (DIRECTORY_SEPARATOR, $save_path) . DIRECTORY_SEPARATOR . $new['name']))
            return $this->configs['debug'] ? error ('OrmUploader 錯誤！', '不明原因錯誤！', '請程式設計者確認狀況！') : false;
        return @unlink ($temp) && self::uploadColumn ($name);
        break;
    }

    return $result;
  }

  // protected function _createNewFiles ($fileInfo, $isUseMoveUploadedFile = false) {

  // }







  // // return sring
  // public function getFileName () {
  //   return uniqid (rand () . '_');
  // }

  // // return array
  // // return string
  // public function url ($key = '') {
  //   if ($this->error)
  //     return $this->configs['debug'] ? call_user_func_array ('error', $this->error) : '';

  //   switch ($this->getBucket ()) {
  //     case 'local':
  //       return ($path = $this->path ($key)) ? base_url ($path) : $this->d4_url ();
  //       break;
  //   }

  //   return $this->configs['debug'] ? error ('OrmUploader 錯誤！', '未知的 bucket，系統尚未支援' . $this->getBucket () . ' 的空間！', '請檢查 config/system/orm_uploader.php 設定檔！') : '';
  // }


  // // return boolean
  // private function _cleanOldFiles ($saveFileName) {
  //   if ($this->error)
  //     return $this->configs['debug'] ? call_user_func_array ('error', $this->error) : false;

  //   if (!(($versions = ($versions = $this->getVersions ()) ? $versions : $this->configs['default_version']) && ($versions = array_keys ($versions))))
  //     return $this->configs['debug'] ? error ('OrmUploader 錯誤！', 'Versions 格式錯誤，請檢查 getVersions () 或者 default_version！', '預設值 default_version 請檢查 config/system/orm_uploader.php 設定檔！') : false;

  //   switch ($this->getBucket ()) {
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

  //   return $this->configs['debug'] ? error ('OrmUploader 錯誤！', '未知的 bucket，系統尚未支援' . $this->getBucket () . ' 的空間！', '請檢查 config/system/orm_uploader.php 設定檔！') : false;
  // }


  // // return boolean
  // public function cleanAllFiles ($isAutoSave = true) {
  //   if ($this->error)
  //     return $this->configs['debug'] ? call_user_func_array ('error', $this->error) : false;

  //   switch ($this->getBucket ()) {
  //     case 'local':
  //       return $this->_cleanOldFiles ('');
  //       break;
  //   }

  //   return $this->configs['debug'] ? error ('OrmUploader 錯誤！', '未知的 bucket，系統尚未支援' . $this->getBucket () . ' 的空間！', '請檢查 config/system/orm_uploader.php 設定檔！') : false;
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

  //   return $this->configs['debug'] ? error ('OrmUploader 錯誤！', '未知的 bucket，系統尚未支援' . $this->getBucket () . ' 的空間！', '請檢查 config/system/orm_uploader.php 設定檔！') : false;
  // }


  // // return array
  // public function save_as ($key, $version) {
  //   if ($this->error)
  //     return $this->configs['debug'] ? call_user_func_array ('error', $this->error) : array ();

  //   if (!($key && $version))
  //     return $this->configs['debug'] ? error ('OrmUploader 錯誤！', '參數錯誤，請檢查 save_as 函式參數！', '請程式設計者確認狀況！') : array ();

  //   if (!(($versions = ($versions = $this->getVersions ()) ? $versions : $this->configs['default_version'])))
  //     return $this->configs['debug'] ? error ('OrmUploader 錯誤！', 'Versions 格式錯誤，請檢查 getVersions () 或者 default_version！', '預設值 default_version 請檢查 config/system/orm_uploader.php 設定檔！') : array ();

  //   switch ($this->getBucket ()) {
  //     case 'local':
  //       if (in_array ($key, array_keys ($versions)))
  //         return is_readable (FCPATH . implode (DIRECTORY_SEPARATOR, $ori_path = array_merge ($this->configs['base_directory'][$this->getBucket ()], $this->getSavePath (), array ($key . $this->configs['separate_symbol'] . (string)$this)))) ? $ori_path : '';

  //       foreach ($versions as $ori_key => $ori_version)
  //         if (is_readable (FCPATH . implode (DIRECTORY_SEPARATOR, $ori_path = array_merge ($this->configs['base_directory'][$this->getBucket ()], $this->getSavePath (), array ($ori_key . $this->configs['separate_symbol'] . ($name = (string)$this))))))
  //           break;

  //       if (!$ori_path)
  //         return $this->configs['debug'] ? error ('OrmUploader 錯誤！', '沒有任何的檔案可以被使用！', '請確認 getVersions () 函式內有存在的檔案可被另存！', '請程式設計者確認狀況！') : array ();

  //       if (!file_exists (FCPATH . implode (DIRECTORY_SEPARATOR, ($path = array_merge ($this->configs['base_directory'][$this->getBucket ()], $this->getSavePath ()))))) {
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

  //   return $this->configs['debug'] ? error ('OrmUploader 錯誤！', '未知的 bucket，系統尚未支援' . $this->getBucket () . ' 的空間！', '請檢查 config/system/orm_uploader.php 設定檔！') : '';
  // }

}
