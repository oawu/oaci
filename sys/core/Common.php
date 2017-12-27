<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2017, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

if (!function_exists ('config')) {
  function config () {
    static $files, $keys;

    if (!(($args = func_get_args ()) && ($fileName = array_shift ($args))))
      // gg ('')
      exit ('找不到該 Config 檔案：' . $fileName);

    if (isset ($keys[$key = $fileName . implode ('_', $args)]))
      return $keys[$key];

    isset ($files[$fileName]) || $files[$fileName] = file_exists ($path = APPPATH . 'config' . DIRECTORY_SEPARATOR . ENVIRONMENT . DIRECTORY_SEPARATOR . $fileName . EXT) || file_exists ($path = APPPATH . 'config' . DIRECTORY_SEPARATOR . $fileName . EXT) ? include_once ($path) : null;

    if ($files[$fileName] === null && !($keys[$key] = null))
      exit ('找不到該 Config 檔案：' . $fileName);

    $t = $files[$fileName];

    foreach ($args as $arg)
      if (!$t = isset ($t[$arg]) ? $t[$arg] : null)
        break;

    return $keys[$key] = $t;
  }
}

if (!function_exists ('remove_invisible_characters')) {
  function remove_invisible_characters ($str, $urlEncoded = true) {
    $n = array ();

    $urlEncoded && array_push ($n, '/%0[0-8bcef]/i', '/%1[0-9a-f]/i', '/%7f/i');

    array_push ($n, '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S');

    do {
      $str = preg_replace ($n, '', $str, -1, $count);
    } while ($count);

    return $str;
  }
}

if (!function_exists ('html_escape')) {
  function html_escape ($var, $doubleEncode = true) {
    if (!$var)
      return $var;

    if (!is_array ($var))
      return htmlspecialchars ($var, ENT_QUOTES, config ('other', 'charset'), $doubleEncode);

    foreach (array_keys ($var) as $key)
      $var[$key] = html_escape ($var[$key], $doubleEncode);

    return $var;
  }
}

if (!function_exists ('stringify_attributes')) {
  function stringify_attributes ($attrs, $js = false) {
    $atts = '';

    if (!$attrs)
      return $atts;
    
    if (is_string ($attrs))
      return ' ' . $attrs;
    
    if (!is_array ($attrs))
      return $atts;

    foreach ($attrs as $key => $val)
      $atts .= $js ? $key . '=' . $val . ',' : ' ' . $key . '="' . $val . '"';

    return rtrim ($atts, ',');
  }
}

if (!function_exists ('is_really_writable')) {
  function is_really_writable ($file) {
    if (DIRECTORY_SEPARATOR === '/' && (is_php ('5.4') || !ini_get ('safe_mode')))
      return is_writable ($file);

    if (is_dir ($file)) {
      if (($fp = @fopen ($file = rtrim ($file, '/') . '/' . md5 (mt_rand ()), 'ab')) === false)
        return false;
 
      fclose ($fp);
      @chmod ($file, 0777);
      @unlink ($file);

      return true;
    }

    if (!is_file ($file) || ($fp = @fopen ($file, 'ab')) === false)
      return false;
 
    fclose ($fp);
    return true;
  }
}

// if (!function_exists ('request_is_cli')) {
//   function request_is_cli () {
//     return PHP_SAPI === 'cli' || defined ('STDIN');
//   }
// }

if (!function_exists ('request_is_https')) {
  function request_is_https () {
    return (!empty ($_SERVER['HTTPS']) && strtolower ($_SERVER['HTTPS']) !== 'off')
        || (isset ($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower ($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https')
        || (!empty ($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower ($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off');
  }
}

if (!function_exists ('request_is_method')) {
  function request_is_method () {
    return strtolower (request_is_cli ()
           ? 'cli'
           : (isset ($_SERVER['REQUEST_METHOD'])
             ? $_SERVER['REQUEST_METHOD']
             : (isset ($_POST['_method'])
               ? $_POST['_method']
               : 'get')));
  }
}

if (!function_exists ('request_is_ajax')) {
  function request_is_ajax () {
    return isset ($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower ($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
  }
}

if (!function_exists ('array_2d_to_1d')) {
  function array_2d_to_1d ($array) {
    $messages = array ();
    foreach ($array as $key => $value)
      if (is_array ($value)) $messages = array_merge ($messages, $value);
      else array_push ($messages, $value);
    return $messages;
  }
}

if (!function_exists ('sort_2d_array')) {
  function sort_2d_array ($key, $list) {
    if ($list) {
      $tmp = array ();
      foreach ($list as &$ma) $tmp[] = &$ma[$key];
      array_multisort ($tmp, SORT_DESC, $list);
    }
    return $list;
  }
}

// if (!function_exists ('cc')) {
//   function cc ($str, $fc = null, $bc = null) {
//     if (!strlen ($str)) return "";
//     // if (!CLI) return $str;

//     $nstr = "";
//     $keys = array ('n' => '30', 'w' => '37', 'b' => '34', 'g' => '32', 'c' => '36', 'r' => '31', 'p' => '35', 'y' => '33');
//     if ($fc && in_array (strtolower ($fc), array_map ('strtolower', array_keys ($keys)))) {
//       $fc = !in_array (ord ($fc[0]), array_map ('ord', array_keys ($keys))) ? in_array (ord ($fc[0]) | 0x20, array_map ('ord', array_keys ($keys))) ? '1;' . $keys[strtolower ($fc[0])] : null : $keys[$fc[0]];
//       $nstr .= $fc ? "\033[" . $fc . "m" : "";
//     }
//     $nstr .= $bc && in_array (strtolower ($bc), array_map ('strtolower', array_keys ($keys))) ? "\033[" . ($keys[strtolower ($bc[0])] + 10) . "m" : "";

//     if (substr ($str, -1) == "\n") { $str = substr ($str, 0, -1); $has_new_line = true; } else { $has_new_line = false; }
//     $nstr .=  $str . "\033[0m";
//     $nstr = $nstr . ($has_new_line ? "\n" : "");

//     return $nstr;
//   }
// }

// if (!function_exists ('_gp')) {
//   function _gp ($type, $msg, $error, $errors = array (), $traces = array ()) {
//     $str = '';
//     $str .= '<!DOCTYPE html><html lang="tw"><head><meta http-equiv="Content-Language" content="zh-tw" /><meta http-equiv="Content-type" content="text/html; charset=utf-8" /><meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui" />';
//     $str .= '<title>' . $type . ($msg ? ' :: ' . $msg : '') . '</title><style type="text/css">*,*:after,*:before{vertical-align:top;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;box-sizing:border-box;-moz-osx-font-smoothing:antialiased;-webkit-font-smoothing:antialiased;-moz-font-smoothing:antialiased;-ms-font-smoothing:antialiased;-o-font-smoothing:antialiased}*::-moz-selection,*:after::-moz-selection,*:before::-moz-selection{color:#fff;background-color:#96c8ff}*::selection,*:after::selection,*:before::selection{color:#fff;background-color:#96c8ff}html{min-height:100%}html body{position:relative;display:inline-block;width:100%;min-height:100%;margin:0;padding:0;color:#5a5a5a;text-align:center;font-size:medium;font-family:Roboto, RobotoDraft, Helvetica, Arial, sans-serif, "微軟正黑體", "Microsoft JhengHei";background:#f0f0f0;-moz-osx-font-smoothing:antialiased;-webkit-font-smoothing:antialiased;-moz-font-smoothing:antialiased;-ms-font-smoothing:antialiased;-o-font-smoothing:antialiased}#main{display:inline-block;width:100%}#main>div{display:inline-block;width:100%;max-width:960px;background-color:white;padding:20px 32px;text-align:left;-moz-border-radius:2px;-webkit-border-radius:2px;border-radius:2px;-moz-box-shadow:0 0 1px #c8c8c8,1px 1px 1px #c8c8c8;-webkit-box-shadow:0 0 1px #c8c8c8,1px 1px 1px #c8c8c8;box-shadow:0 0 1px #c8c8c8,1px 1px 1px #c8c8c8;margin-top:16px;margin-bottom:16px}@media screen and (max-width: 959px) and (min-width: 0){#main>div{margin-bottom:0;margin-top:0}}@media screen and (max-width: 749px) and (min-width: 0){#main>div{padding:20px}}@media screen and (max-width: 499px) and (min-width: 0){#main>div{padding:12px}}#main>div>h1,#main>div>h2,#main>div>h3{position:relative;font-size:20px;display:inline-block;width:100%;height:32px;line-height:32px;margin:0;margin-top:4px}#main>div>h1:not(:first-child),#main>div>h2:not(:first-child),#main>div>h3:not(:first-child){margin-top:32px}#main>div>h1:before,#main>div>h2:before,#main>div>h3:before{position:absolute;left:0;top:0;display:none;width:32px;line-height:32px;height:32px;text-align:center;color:#969682;font-weight:bold}#main>div>h1:after,#main>div>h2:after,#main>div>h3:after{display:none;width:100%;height:14px;line-height:14px;font-size:13px;font-weight:normal;color:#828282}#main>div>h1[data-font]:not([data-font=""]),#main>div>h2[data-font]:not([data-font=""]),#main>div>h3[data-font]:not([data-font=""]){padding-left:48px}#main>div>h1[data-font]:not([data-font=""]):before,#main>div>h2[data-font]:not([data-font=""]):before,#main>div>h3[data-font]:not([data-font=""]):before{display:inline-block;content:attr(data-font);-moz-transform:rotate(90deg);-ms-transform:rotate(90deg);-webkit-transform:rotate(90deg);transform:rotate(90deg);font-size:32px;line-height:30px}#main>div>h1[data-font]:not([data-font=""])[data-msg]:not([data-msg=""]),#main>div>h2[data-font]:not([data-font=""])[data-msg]:not([data-msg=""]),#main>div>h3[data-font]:not([data-font=""])[data-msg]:not([data-msg=""]){padding-left:64px}#main>div>h1[data-icon]:not([data-icon=""]),#main>div>h2[data-icon]:not([data-icon=""]),#main>div>h3[data-icon]:not([data-icon=""]){padding-left:48px}#main>div>h1[data-icon]:not([data-icon=""]):before,#main>div>h2[data-icon]:not([data-icon=""]):before,#main>div>h3[data-icon]:not([data-icon=""]):before{display:inline-block;content:attr(data-icon);-moz-transform:rotate(0);-ms-transform:rotate(0);-webkit-transform:rotate(0);transform:rotate(0);font-size:32px;line-height:30px}#main>div>h1[data-icon]:not([data-icon=""])[data-msg]:not([data-msg=""]),#main>div>h2[data-icon]:not([data-icon=""])[data-msg]:not([data-msg=""]),#main>div>h3[data-icon]:not([data-icon=""])[data-msg]:not([data-msg=""]){padding-left:64px}#main>div>h1[data-msg]:not([data-msg=""]),#main>div>h2[data-msg]:not([data-msg=""]),#main>div>h3[data-msg]:not([data-msg=""]){height:48px}#main>div>h1[data-msg]:not([data-msg=""]):before,#main>div>h2[data-msg]:not([data-msg=""]):before,#main>div>h3[data-msg]:not([data-msg=""]):before{width:48px;line-height:50px;height:48px;font-size:48px}#main>div>h1[data-msg]:not([data-msg=""]):after,#main>div>h2[data-msg]:not([data-msg=""]):after,#main>div>h3[data-msg]:not([data-msg=""]):after{display:inline-block;content:attr(data-msg)}#main>div>h2{font-size:18px}#main>div>h3{font-size:16px}#main>div>i{display:block;width:calc(100% + 32px * 2);margin-left:-32px;height:1px;margin-top:20px;margin-bottom:32px;border-top:1px solid #e6e6e6}@media screen and (max-width: 749px) and (min-width: 0){#main>div>i{width:calc(100% + 10px * 2);margin-left:-10px}}@media screen and (max-width: 499px) and (min-width: 0){#main>div>i{width:calc(100% + 6px * 2);margin-left:-6px}}#main>div>i+*{margin-top:0 !important}#main>div>i+i{display:none}#main>div>blockquote{display:inline-block;width:100%;margin:0;padding:8px 16px;border-left:3px solid #c8c8c8;margin-top:32px;color:#787878}#main>div>blockquote+table{margin-top:20px}#main>div>blockquote+blockquote{margin-top:16px}#main>div>table{margin:0;margin-top:12px;width:100%;border-spacing:0;border-collapse:separate;color:#2e2f30;font-size:14px}#main>div>table td,#main>div>table th{word-break:break-all;word-break:break-word;line-height:20px}#main>div>table td.c,#main>div>table th.c{text-align:center}#main>div>table td.l,#main>div>table th.l{text-align:left}#main>div>table td.r,#main>div>table th.r{text-align:right}#main>div>table thead tr{height:32px}#main>div>table thead tr th{padding:6px;background-color:#e8e8e8;border-left:1px solid #dedede}#main>div>table thead tr th:last-child{border-right:1px solid #dedede}#main>div>table thead tr:first-child th{border-top:1px solid #dedede}#main>div>table thead+tbody tr:nth-child(2n+1){background-color:#fff}#main>div>table thead+tbody tr:nth-child(2n){background-color:#f7f7f7}#main>div>table thead+tbody:nth-child(2n+1){background-color:#f7f7f7}#main>div>table thead+tbody:nth-child(2n){background-color:#fff}#main>div>table tbody tr{min-height:32px;background-color:#fff}#main>div>table tbody tr td,#main>div>table tbody tr th{padding:6px;border-top:1px solid #dedede;border-left:1px solid #dedede;vertical-align:middle}#main>div>table tbody tr td:last-child,#main>div>table tbody tr th:last-child{border-right:1px solid #dedede}#main>div>table tbody tr td>i,#main>div>table tbody tr th>i{filter:progid:DXImageTransform.Microsoft.Alpha(Opacity=75);opacity:.75;font-size:13px}#main>div>table tbody tr td .p,#main>div>table tbody tr th .p{display:inline-block;position:relative;font-size:0;white-space:nowrap;width:20px;margin-right:2px;overflow:hidden;-moz-transition:width .3s;-o-transition:width .3s;-webkit-transition:width .3s;transition:width .3s;color:#646464}#main>div>table tbody tr td .p.s,#main>div>table tbody tr th .p.s{width:auto;font-size:14px}#main>div>table tbody tr td .p.s:after,#main>div>table tbody tr th .p.s:after{content:"";background-color:rgba(255,255,255,0)}#main>div>table tbody tr td .p:after,#main>div>table tbody tr th .p:after{content:".../";position:absolute;left:0;top:0;display:inline-block;width:100%;height:100%;font-size:14px;text-align:center;cursor:pointer;-moz-transition:background-color .3s;-o-transition:background-color .3s;-webkit-transition:background-color .3s;transition:background-color .3s}#main>div>table tbody tr th{background-color:#f7f7f7;text-align:right}#main>div>table tbody tr:last-child td,#main>div>table tbody tr:last-child th{border-bottom:1px solid #dedede}#main>div>div{text-align:right;color:#787878;font-size:12px;display:inline-block;width:100%;height:20px;line-height:20px}#main>div>div a{display:inline;font-weight:normal;text-decoration:none;-moz-transition:color .3s,border-bottom .3s;-o-transition:color .3s,border-bottom .3s;-webkit-transition:color .3s,border-bottom .3s;transition:color .3s,border-bottom .3s;color:#4285f4;border-bottom:1px solid #4285f4;font-size:11px;border-bottom-color:rgba(70,136,241,0.4);border-bottom-style:dashed;-moz-transition:border-bottom-color .3s,color .3s;-o-transition:border-bottom-color .3s,color .3s;-webkit-transition:border-bottom-color .3s,color .3s;transition:border-bottom-color .3s,color .3s}#main>div>div a.active,#main>div>div a:hover{color:#0d5bdd;border-bottom:1px solid #0d5bdd}#main>div>div a:hover{border-bottom-color:rgba(70,136,241,0.8);border-bottom-style:dashed}</style><script type="text/javascript">function init () { document.querySelectorAll ("a.p").forEach (function () { this.onclick = function (e) { e.srcElement.classList.toggle ("s") }; }); }</script></head><body lang="zh-tw" onload="init ();"><main id="main"><div>';
//     $str .= '<h1 data-font=": ("' . ($msg ? ' data-msg="' . $msg . '"' : '') . '>' . $type . '</h1><i></i>';
//     empty ($error) || $str .= '<blockquote>' . $error . '</blockquote>';

//     if ($errors) {
//       $str .= '<table><tbody>';
//         foreach ($errors as $key => $error)
//           $str .= '<tr><th width="100">' . $key . '</th><td>' . $error . '</td></tr>';
//       $str .= '</tbody></table>';
//     }

//     $str .= $traces ? '<h2>回朔追蹤</h2><table><thead><tr><th width="50" class="c">順序</th><th width="250">標題</th><th>內容</th></tr></thead>' . implode ('', array_map (function ($trace, $i) { $trace[0] = str_replace (FCPATH, '', $trace[0]); $dir = pathinfo ($trace[0], PATHINFO_DIRNAME); $base = pathinfo ($trace[0], PATHINFO_BASENAME); return '<tr><td class="c"><i>#' . ($i + 1) . '</i></td><td>' . ($dir && $dir != '.'  ? '<a class="p">' . $dir . DIRECTORY_SEPARATOR . '</a>' : '') . $base . '</td><td>' . $trace[1] . '</td></tr>'; }, $traces, ($ks = array_keys ($traces)) && rsort ($ks) ? $ks : $ks)) . '</tbody></table>' : '';
//     $str .= '<i></i><div>©2014 - 2017 <a href="https://www.ioa.tw/" target="_blank">OA Wu</a>, All Rights Reserved.</div></div></main></body></html>';

//     return $str;
//   }
// }
// if (!function_exists ('_gpd')) {
//   function _gpd () { echo call_user_func_array ('_gp', func_get_args ()); exit; }
// }




// gg (
//   '503 Service Unavailable.', array (
//     'font' => '⚠',
//     'text' => '錯誤',
//     'msg' => '503 Service Unavailable'
//     ), array (
//     'quote' => '不存在的 $system_path',
//     'detail' => array (
//         '物件' => 'ActiveRecord\DatabaseException',
//         '訊息' => 'SQLSTATE[HY000] [1049] Unknown database "oaciw"',
//         '檔案' => '/Users/OA/www/ci316/sys/model/lib/Connection.php(262)'
//       ),
//     'traces' => array (
//         'sys/model/lib/Connection.php(122)' => 'ActiveRecord\Connection->__construct(stdClass)',
//         'sys/model/lib/Connection.php(12)' => 'ActiveRecord\Connection->__construct(stdClass)',
//         'sys/model/lib/Connection.php(2)' => 'ActiveRecord\Connection->__construct(stdClass)'
//       )
//     ), array (
//       'string' => 'HTTP/1.1 503 Service Unavailable.',
//       'replace' => true,
//       'code' => '503'
//     ));