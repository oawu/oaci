<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

class Utf8 {
  public static function init () {
    define ('UTF8_ENABLED', defined ('PREG_BAD_UTF8_ERROR') && (ICONV_ENABLED === true || MB_ENABLED === true) && (($t = Config::get ('general', 'charset')) ? $t : 'UTF-8') === 'UTF-8');
  }
  public static function cleanString ($str) {
    return self::isAscii ($str) === false ? MB_ENABLED ? mb_convert_encoding ($str, 'UTF-8', 'UTF-8') : (ICONV_ENABLED ? @iconv('UTF-8', 'UTF-8//IGNORE', $str) : $str) : $str;
  }

  public static function safeAsciiForXml($str) {
    return removeInvisibleCharacters($str, false);
  }

  public static function convert2utf8 ($str, $encoding) {
    return MB_ENABLED ? mb_convert_encoding($str, 'UTF-8', $encoding) : (ICONV_ENABLED ? @iconv($encoding, 'UTF-8', $str) : false);
  }

  public static function isAscii ($str) {
    return (preg_match ('/[^\x00-\x7F]/S', $str) === 0);
  }
}
