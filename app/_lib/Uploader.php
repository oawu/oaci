<?php defined ('BASEPATH') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2017, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class UploaderException extends Exception {}

class Uploader {
  private static $debug = false;
  
  private $driverConfigs = array ();
  private $tmpDir = null;

  protected $orm = null;
  protected $column = null;
  protected $value = null;

  public function __construct ($orm, $column) {
    $attrs = array_keys ($orm->attributes ());
    in_array ($column, $attrs) || Uploader::mustError ('[Uploader] Class 「' . get_class ($orm) . '」 無 「' . $column . '」 欄位。');
    in_array ($this->uniqueColumn (), $attrs) || Uploader::mustError ('[Uploader] Class 「' . get_class ($orm) . '」 無 「' . $this->uniqueColumn () . '」 欄位。');

    $this->orm = $orm;
    $this->column = $column;
    $this->value = $orm->$column;
    $orm->$column = $this;

    is_really_writable ($this->tmpDir = config ('uploader', 'tmp_dir')) || Uploader::mustError ('[Uploader] Tmp 目錄沒有權限寫入。Path：' . $this->tmpDir);
    ($this->driverConfigs = config ('uploader', 'drivers', $this->getDriver ())) || Uploader::mustError ('[Uploader] Driver 設定錯誤。');
    
    if ($this->getDriver () == 's3') {
      !class_exists ('S3', false) && !Load::lib ('S3.php') && Uploader::mustError ('[Uploader] 導入 S3 物件失敗。');
      S3::init ($this->driverConfigs['access_key'], $this->driverConfigs['secret_key']);
    }
  }

  protected function uniqueColumn () {
    return 'id';
  }

  protected function getDriver () {
    return config ('uploader', 'driver');
  }
  
  public static function mkdir ($pathname, $mode = 0777, $recursive = false) {
    $oldmask = umask (0);
    @mkdir ($pathname, $mode, $recursive);
    umask ($oldmask);
  }
  
  public static function chmod ($pathname, $mode = 0777) {
    $oldmask = umask (0);
    @chmod ($pathname, $mode);
    umask ($oldmask);
  }
  
  public static function mustError () {
    throw new UploaderException (call_user_func_array ('sprintf', func_get_args ()));
  }
  
  public static function error () {
    if (self::$debug)
      throw new UploaderException (call_user_func_array ('sprintf', func_get_args ()));
    else
      return false;
  }

  public function __toString () {
    return  $this->getValue ();
  }
  
  public function getValue () {
    return (String)$this->value;
  }

  public static function bind ($column, $class = null) {
    ($trace = debug_backtrace (DEBUG_BACKTRACE_PROVIDE_OBJECT)) || Uploader::mustError ('[Uploader] 取得 debug_backtrace 發生錯誤。');
    isset ($trace[1]['object']) && is_object ($orm = $trace[1]['object']) || Uploader::mustError ('[Uploader] 取得 debug_backtrace 回傳結構有誤，無法取得上層物件。');
    class_exists ($class) || $class = get_called_class ();

    return new $class ($orm, $column);
  }

  // ============================================================

  protected function d4Url () {
    return isset ($this->driverConfigs['d4_url']) ? $this->driverConfigs['d4_url'] : '';
  }

  public function url ($key = '') {
    switch ($this->getDriver ()) {
      case 'local':
        return ($path = $this->path ($key)) ? implode ('/', array_merge (array (rtrim ($this->driverConfigs['base_url'], '/')) , $path)) : $this->d4Url ();
        break;
      
      case 's3':
        return ($path = $this->path ($key)) ? implode ('/', array_merge (array (rtrim ($this->driverConfigs['base_url'], '/')) , $path)) : $this->d4Url ();
        break;
    }
  }

  public function path ($fileName = '') {
    switch ($this->getDriver ()) {
      case 'local':
        return is_readable (FCPATH . implode (DIRECTORY_SEPARATOR, $path = array_merge ($this->getBaseDirectory (), $this->getSavePath (), array ($fileName)))) ? $path : array ();
        break;

      case 's3':
        return array_merge ($this->getBaseDirectory (), $this->getSavePath (), array ($fileName));
        break;
    }
  }

  protected function getBaseDirectory () {
    return $this->driverConfigs['base_dir'];
  }
  
  public function getSavePath () {
    return is_numeric ($id = $this->getColumnValue ($this->uniqueColumn ())) ? array_merge (array ($this->getTableName (), $this->getColumnName ()), array_map (function ($t) { return ($t = ltrim ($t, 0)) ? $t : '0'; }, str_split (sprintf('%016s', dechex($id)), 2))) : array ($this->getTableName (), $this->getColumnName ());
  }
  
  protected function getColumnValue ($column) {
    $attrs = array_keys ($this->orm->attributes ());
    return in_array ($column, $attrs) ? $this->orm->$column : '';
  }
  
  protected function getTableName () {
    return $this->orm->table ()->table;
  }
  
  protected function getColumnName () {
    return $this->column;
  }
  
  protected function getRandomName () {
    return md5 (uniqid (mt_rand (), true));
  }
  
  public function put ($fileInfo) {
    if (!($fileInfo && (is_array ($fileInfo) || (is_string ($fileInfo) && file_exists ($fileInfo)))))
      return Uploader::error ('[Uploader] put 格式有誤。');

    $isUseMoveUploadedFile = false;

    if (is_array ($fileInfo)) {
      foreach (array ('name', 'tmp_name', 'type', 'error', 'size') as $key)
        if (!isset ($fileInfo[$key]))
          return false;

      $name = $fileInfo['name'];
      $isUseMoveUploadedFile = true;
    } else {
      $name = basename ($fileInfo);
      $fileInfo = array ('name' => 'file', 'tmp_name' => $fileInfo, 'type' => '', 'error' => '', 'size' => '1');
    }

    $name = preg_replace ("/[^a-zA-Z0-9\\._-]/", "", $name);
    $format = ($format = pathinfo ($name, PATHINFO_EXTENSION)) ? '.' . $format : '';
    $name = ($name = pathinfo ($name, PATHINFO_FILENAME)) ? $name . $format : $this->getRandomName () . $format;

    if (!$temp = $this->moveOriFile ($fileInfo, $isUseMoveUploadedFile))
      return Uploader::error ('[Uploader] 搬移至暫存資料夾時發生錯誤。');

    if (!$savePath = $this->verifySavePath ())
      return Uploader::error ('[Uploader] 確認儲存路徑發生錯誤。');

    if (!$result = $this->moveFileAndUploadColumn ($temp, $savePath, $name))
      return Uploader::error ('[Uploader] 搬移預設位置時發生錯誤。');

    return $result;
  }

  private function moveOriFile ($fileInfo, $isUseMoveUploadedFile) {
    $temp = $this->tmpDir . 'uploader_' . $this->getRandomName ();

    if ($isUseMoveUploadedFile)
      @move_uploaded_file ($fileInfo['tmp_name'], $temp);
    else
      @rename ($fileInfo['tmp_name'], $temp);

    Uploader::chmod ($temp, 0777);

    if (!file_exists ($temp))
      return Uploader::error ('[Uploader] moveOriFile 移動檔案失敗。Path：' . $temp);

    return $temp;
  }

  private function verifySavePath () {
    switch ($this->getDriver ()) {
      case 'local':
        if (!is_really_writable ($path = FCPATH . implode (DIRECTORY_SEPARATOR, $this->getBaseDirectory ())))
          return Uploader::error ('[Uploader] verifySavePath 資料夾不能儲存。Path：' . $path);

        if (!file_exists ($t = FCPATH . implode (DIRECTORY_SEPARATOR, $path = array_merge ($this->getBaseDirectory (), $this->getSavePath ()))))
          Uploader::mkdir ($t, 0777, true);

        return is_really_writable ($t) ? $path : Uploader::error ('[Uploader] verifySavePath 資料夾不能儲存。Path：' . $path);
        break;

      case 's3':
        return array_merge ($this->getBaseDirectory (), $this->getSavePath ());
        break;
    }
    return false;
  }

  protected function moveFileAndUploadColumn ($temp, $savePath, $oriName) {
    switch ($this->getDriver ()) {
      case 'local':
        return $this->uploadColumnAndUpload ('') && rename ($temp, $savePath = FCPATH . implode (DIRECTORY_SEPARATOR, $savePath) . DIRECTORY_SEPARATOR . $oriName) ? $this->uploadColumnAndUpload ($oriName) : Uploader::error ('[Uploader] moveFileAndUploadColumn 搬移預設位置時發生錯誤。');
        break;

      case 's3':
        return $this->uploadColumnAndUpload ('') && S3::putObject ($temp, $this->driverConfigs['bucket'], implode ('/', $savePath) . '/' . $oriName) ? $this->uploadColumnAndUpload ($oriName) && @unlink ($temp) : Uploader::error ('[Uploader] moveFileAndUploadColumn 搬移預設位置時發生錯誤。');
        break;
    }
    return false;
  }
  
  protected function uploadColumnAndUpload ($value, $isSave = true) {
    if (!$this->cleanOldFile ())
      return Uploader::error ('[Uploader] uploadColumnAndUpload 清除檔案發生錯誤。');
    return $isSave ? $this->uploadColumn ($value) : true;
  }
  
  protected function cleanOldFile () {
    switch ($this->getDriver ()) {
      case 'local':
        if ($paths = $this->getAllPaths ())
          foreach ($paths as $path)
            if (is_file ($path = FCPATH . implode (DIRECTORY_SEPARATOR, $path)) && is_writable ($path))
              if (!@unlink ($path))
                return Uploader::error ('[Uploader] cleanOldFile 清除檔案發生錯誤。Path：' . $path);
        return true;
        break;
      
      case 's3':
        if ($paths = $this->getAllPaths ())
          foreach ($paths as $path)
            if (!S3::deleteObject ($this->driverConfigs['bucket'], implode ('/', $path)))
              return Uploader::error ('[Uploader] cleanOldFile 清除檔案發生錯誤。Path：' . $path);
        return true;
        break;
    }
    return false;
  }
  
  public function getAllPaths () {
    if (!$this->getValue ())
      return array ();

    switch ($this->getDriver ()) {
      case 'local':
        return array (array_merge ($this->getBaseDirectory (), $this->getSavePath (), array ($this->getValue ())));
        break;

      case 's3':
        return array (array_merge ($this->getBaseDirectory (), $this->getSavePath (), array ($this->getValue ())));
        break;
    }

    return array ();
  }
  
  protected function uploadColumn ($value) {
    $column = $this->column;
    $this->orm->$column = $value;

    if (!$this->orm->save ())
      return false;

    $this->value = $value;
    $this->orm->$column = $this;
    return true;
  }

  public function cleanAllFiles ($isSave = true) {
    return $this->uploadColumnAndUpload ('');
  }

  public function putUrl ($url) {
    Load::sysFunc ('download.php');
    $format = pathinfo ($url, PATHINFO_EXTENSION);
    $temp = $this->tmpDir . implode (DIRECTORY_SEPARATOR, array ($this->getRandomName () . ($format ? '.' . $format : '')));
    return ($temp = download_web_file ($url, $temp)) && $this->put ($temp, false) ? file_exists ($temp) ? @unlink ($temp) : true : false;
  }
}

class FileUploader extends Uploader {
  public function __construct ($orm, $column) {
    parent::__construct ($orm, $column);
  }

  public function url ($url ='') {
    return parent::url ('');
  }

  public function path ($fileName = '') {
    return parent::path ($this->getValue ());
  }
}




class ImageUploader extends Uploader {
  private $configs = array ();

  public function __construct ($orm, $column) {
    parent::__construct ($orm, $column);

    $this->configs = config ('uploader', 'image_utility');
    // $this->CI->load->library ('image/ImageUtility');
  }
  
  protected function getVirtualVersions () {
    return array ();
  }

  protected function getVersions () {
    return $this->configs['default_version'];
  }

  public function path ($key = '') {
    return ($versions = ($versions = array_merge ($this->getVersions (), $this->getVirtualVersions ())) ? $versions : $this->configs['default_version']) && isset ($versions[$key]) && ($value = $this->getValue ()) && ($fileName = $key . $this->configs['separate_symbol'] . $value) ? parent::path ($fileName) : array ();
  }















  // // return boolean
  // private function _utility ($image, $save, $key, $version) {
  //   if ($version)
  //     if (is_callable (array ($image, $method = array_shift ($version))))
  //       call_user_func_array (array ($image, $method), $version);
  //     else
  //       return $this->getDebug () ? error ('OrmImageUploader 錯誤！', 'ImageUtility 無法呼叫的 method，method：' . $method, '請程式設計者確認狀況！') : '';
  //   return $image->save ($save, true);
  // }
  // // return array
  // public function getAllPaths () {
  //   if ($this->error)
  //     return $this->getDebug () ? call_user_func_array ('error', $this->error) : array ();

  //   if (!($versions = ($versions = array_merge ($this->getVersions (), $this->getVirtualVersions ())) ? $versions : $this->configs['default_version']))
  //     return $this->getDebug () ? error ('OrmImageUploader 錯誤！', 'Versions 格式錯誤，請檢查 getVersions () 或者 default_version！', '預設值 default_version 請檢查 config/system/orm_uploader.php 設定檔！') : '';

  //   $paths = array ();

  //   switch ($this->getDriver ()) {
  //     case 'local':
  //       foreach ($versions as $key => $version)
  //         if (is_writable (implode (DIRECTORY_SEPARATOR, $path = array_merge ($this->getBaseDirectory (), $this->getSavePath (), array ($key . $this->configs['separate_symbol'] . $this->getValue ())))))
  //           array_push ($paths, $path);
  //       return $paths;
  //       break;

  //     case 's3':
  //       foreach ($versions as $key => $version)
  //         array_push ($paths, array_merge ($this->getBaseDirectory (), $this->getSavePath (), array ($key . $this->configs['separate_symbol'] . $this->getValue ())));
  //       return $paths;
  //       break;
  //   }
  //   return $this->getDebug () ? error ('OrmUploader 錯誤！', '未知的 driver，系統尚未支援 ' . $this->getDriver () . ' 的空間！', '請檢查 config/system/orm_uploader.php 設定檔！') : array ();
  // }
  // // return boolean
  // protected function moveFileAndUploadColumn ($temp, $save_path, $ori_name) {
  //   if ($this->error)
  //     return $this->getDebug () ? call_user_func_array ('error', $this->error) : '';

  //   if (!($versions = ($versions = $this->getVersions ()) ? $versions : $this->configs['default_version']))
  //     return $this->getDebug () ? error ('OrmImageUploader 錯誤！', 'Versions 格式錯誤，請檢查 getVersions () 或者 default_version！', '預設值 default_version 請檢查 config/system/orm_uploader.php 設定檔！') : '';

  //   if (!is_writable (FCPATH . implode (DIRECTORY_SEPARATOR, $path = $this->getTempDirectory ())))
  //     return $this->getDebug () ? error ('OrmImageUploader 錯誤！', '暫存資料夾不可讀寫或不存在！', '請檢查暫存資料夾是否存在以及可讀寫！', '預設值 暫存資料夾 請檢查 config/system/orm_uploader.php 設定檔！') : false;

  //   $news = array ();
  //   $info = @exif_read_data ($temp);
  //   $orientation = $info && isset ($info['Orientation']) ? $info['Orientation'] : 0;

  //   try {
  //     foreach ($versions as $key => $version) {
  //       $image = ImageUtility::create ($temp, null);
  //       $image->rotate ($orientation == 6 ? 90 : ($orientation == 8 ? -90 : ($orientation == 3 ? 180 : 0)));
        
  //       $name = !isset ($name) ? $this->getRandomName () . ($this->configs['auto_add_format'] ? '.' . $image->getFormat () : '') : $name;
  //       $new_name = $key . $this->configs['separate_symbol'] . $name;
  //       $new_path = FCPATH . implode (DIRECTORY_SEPARATOR, array_merge ($path, array ($new_name)));
  //       array_push ($news, array ('name' => $new_name, 'path' => $new_path));

  //       if (!$this->_utility ($image, $new_path, $key, $version))
  //         return $this->getDebug () ? error ('OrmImageUploader 錯誤！', '圖想處理失敗！', '請程式設計者確認狀況！') : false;
  //     }
  //   } catch (Exception $e) {
  //     return $this->getDebug () ? call_user_func_array ('error', $e->getMessages ()) : '';
  //   }

  //   if (count ($news) != count ($versions))
  //     return $this->getDebug () ? error ('OrmImageUploader 錯誤！', '不明原因錯誤！', '請程式設計者確認狀況！') : false;

  //   switch ($this->getDriver ()) {
  //     case 'local':
  //       @self::uploadColumnAndUpload ('');

  //       foreach ($news as $new)
  //         if (!@rename ($new['path'], FCPATH . implode (DIRECTORY_SEPARATOR, $save_path) . DIRECTORY_SEPARATOR . $new['name']))
  //           return $this->getDebug () ? error ('OrmImageUploader 錯誤！', '不明原因錯誤！', '請程式設計者確認狀況！') : false;
  //       return self::uploadColumnAndUpload ($name) && @unlink ($temp);
  //       break;

  //     case 's3':
  //       @self::uploadColumnAndUpload ('');
  //       foreach ($news as $new)
  //         if (!(S3::putFile ($new['path'], $this->getS3Bucket (), implode (DIRECTORY_SEPARATOR, $save_path) . DIRECTORY_SEPARATOR . $new['name']) && @unlink ($new['path'])))
  //           return $this->getDebug () ? error ('OrmImageUploader 錯誤！', '不明原因錯誤！', '請程式設計者確認狀況！') : false;
  //       return self::uploadColumnAndUpload ($name) && @unlink ($temp);
  //       break;
  //   }

  //   return $this->getDebug () ? error ('OrmImageUploader 錯誤！', '未知的 driver，系統尚未支援 ' . $this->getDriver () . ' 的空間！', '請檢查 config/system/orm_uploader.php 設定檔！') : false;
  // }



  // // return array
  // public function save_as ($key, $version) {
  //   if ($this->error)
  //     return $this->getDebug () ? call_user_func_array ('error', $this->error) : array ();

  //   if (!($key && $version))
  //     return $this->getDebug () ? error ('OrmImageUploader 錯誤！', '參數錯誤，請檢查 save_as 函式參數！', '請程式設計者確認狀況！') : array ();

  //   if (!(($versions = ($versions = $this->getVersions ()) ? $versions : $this->configs['default_version'])))
  //     return $this->getDebug () ? error ('OrmImageUploader 錯誤！', 'Versions 格式錯誤，請檢查 getVersions () 或者 default_version！', '預設值 default_version 請檢查 config/system/orm_uploader.php 設定檔！') : array ();

  //   if (in_array ($key, $keys = array_keys ($versions)))
  //     return $this->getDebug () ? error ('OrmImageUploader 錯誤！', '已經有相符合的 key 名稱，key：' . $key, '目前的 key 有：' . implode (', ', $keys)) : array ();

  //   switch ($this->getDriver ()) {
  //     case 'local':
  //       foreach ($versions as $ori_key => $ori_version)
  //         if (is_readable (FCPATH . implode (DIRECTORY_SEPARATOR, $ori_path = array_merge ($this->getBaseDirectory (), $this->getSavePath (), array ($ori_key . $this->configs['separate_symbol'] . ($name = $this->getValue ()))))))
  //           break;

  //       if (!$ori_path)
  //         return $this->getDebug () ? error ('OrmImageUploader 錯誤！', '沒有任何的檔案可以被使用！', '請確認 getVersions () 函式內有存在的檔案可被另存！', '請程式設計者確認狀況！') : array ();

  //       if (!file_exists (FCPATH . implode (DIRECTORY_SEPARATOR, ($path = array_merge ($this->getBaseDirectory (), $this->getSavePath ()))))) {
  //         $oldmask = umask (0);
  //         @mkdir (FCPATH . implode (DIRECTORY_SEPARATOR, $path), 0777, true);
  //         umask ($oldmask);
  //       }

  //       if (!is_writable (FCPATH . implode (DIRECTORY_SEPARATOR, $path)))
  //         return $this->getDebug () ? error ('OrmImageUploader 錯誤！', '資料夾不能儲存！路徑：' . $path, '請程式設計者確認狀況！') : '';

  //       try {
  //         $image = ImageUtility::create (FCPATH . implode (DIRECTORY_SEPARATOR, $ori_path), null);
  //         $path = array_merge ($path, array ($key . $this->configs['separate_symbol'] . $name));

  //         if ($this->_utility ($image, FCPATH . implode (DIRECTORY_SEPARATOR, $path), $key, $version))
  //           return $path;
  //         else
  //           return array ();
  //       } catch (Exception $e) {
  //         return $this->getDebug () ? call_user_func_array ('error', $e->getMessages ()) : '';
  //       }
  //       break;

  //     case 's3':
  //       if (!@S3::getObject ($this->getS3Bucket (), implode (DIRECTORY_SEPARATOR, array_merge ($path = array_merge ($this->getBaseDirectory (), $this->getSavePath ()), array ($fileName = array_shift (array_keys ($versions)) . $this->configs['separate_symbol'] . ($name = $this->getValue ())))), FCPATH . implode (DIRECTORY_SEPARATOR, $fileName = array_merge ($this->getTempDirectory (), array ($fileName))))) 
  //         return $this->getDebug () ? error ('OrmImageUploader 錯誤！', '沒有任何的檔案可以被使用！', '請確認 getVersions () 函式內有存在的檔案可被另存！', '請程式設計者確認狀況！') : array ();

  //       try {
  //         $image = ImageUtility::create ($fileName = FCPATH . implode (DIRECTORY_SEPARATOR, $fileName), null);
  //         $newPath = array_merge ($path, array ($newName = $key . $this->configs['separate_symbol'] . $name));

  //         if ($this->_utility ($image, FCPATH . implode (DIRECTORY_SEPARATOR, $newFileName = array_merge ($this->getTempDirectory (), array ($newName))), $key, $version) && S3::putFile ($newFileName = FCPATH . implode (DIRECTORY_SEPARATOR, $newFileName), $this->getS3Bucket (), implode (DIRECTORY_SEPARATOR, $newPath)) && @unlink ($newFileName) && @unlink ($fileName))
  //           return $newPath;  
  //         else
  //           return array ();
  //       } catch (Exception $e) {
  //         return $this->getDebug () ? call_user_func_array ('error', $e->getMessages ()) : '';
  //       }
  //       break;
  //   }

  //   return $this->getDebug () ? error ('OrmImageUploader 錯誤！', '未知的 driver，系統尚未支援 ' . $this->getDriver () . ' 的空間！', '請檢查 config/system/orm_uploader.php 設定檔！') : array ();
  // }
}
