<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 */

class PttGeter {
  private static $ci = null;
  public function __construct () {
  }
  private static function ci () {
    if (self::$ci !== null) return self::$ci;
    self::$ci =& get_instance ();
    return self::$ci;
  }
  public static function userAgent () {
    $t = array (
      'Mozilla/5.0 (iPhone; CPU iPhone OS 9_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13B143 Safari/601.1',
      'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/32.0.1700.76 Safari/537.36',
      'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36',
      'Mozilla/5.0 (Linux; Android 4.3; Nexus 7 Build/JSS15Q) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36',
      'Mozilla/5.0 (iPhone; CPU iPhone OS 9_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13B143 Safari/601.1',
    );
    return $t[array_rand ($t)];
  }
  public static function getOver18Cookie () {
    $url = 'https://www.ptt.cc/ask/over18';

    $options = array (
      CURLOPT_URL => $url,
      CURLOPT_USERAGENT => self::userAgent (),
      CURLOPT_POSTFIELDS => http_build_query (array (
        'from' => '/bbs/Gossiping/index.html',
        'yes' => 'yes',
      )),
      CURLOPT_POST => true, CURLOPT_HEADER => true, CURLOPT_TIMEOUT => 120, CURLOPT_MAXREDIRS => 10, CURLOPT_AUTOREFERER => true, CURLOPT_CONNECTTIMEOUT => 30, CURLOPT_RETURNTRANSFER => true, CURLOPT_FOLLOWLOCATION => true,
    );

    $ch = curl_init ($url);
    curl_setopt_array ($ch, $options);
    $data = curl_exec ($ch);
    $error = curl_error ($ch);
    curl_close ($ch);
      
    if ($error) throw new Exception ('Login 有誤(0)！Msg:' . $error);
    if (!$data) throw new Exception ('Login 有誤(1)！');

    preg_match_all ('/^Set-Cookie:\s*([^;]*)/mi', $data, $matches);
    if (!isset ($matches[1][2])) throw new Exception ('Login 有誤(2)！');
    
    return $matches[1][2];
  }
  public static function getListAndPrevNextUri ($uri, $cookie = '') {
    $cookie = ($cookie ? $cookie . '; ' : '') . 'over18=1;';

    $url = 'https://www.ptt.cc/' . ltrim ($uri, '/');
    $options = array (
      CURLOPT_URL => $url,
      CURLOPT_COOKIE => $cookie,
      CURLOPT_USERAGENT => self::userAgent (),
      CURLOPT_TIMEOUT => 120, CURLOPT_HEADER => false, CURLOPT_POST => true, CURLOPT_MAXREDIRS => 10, CURLOPT_AUTOREFERER => true, CURLOPT_CONNECTTIMEOUT => 30, CURLOPT_RETURNTRANSFER => true, CURLOPT_FOLLOWLOCATION => true,
    );

    $ch = curl_init ($url);
    curl_setopt_array ($ch, $options);
    $data = curl_exec ($ch);
    $error = curl_error ($ch);
    curl_close ($ch);

    if ($error) return $error;

    PttGeter::ci ()->load->library ('phpQuery');
    $query = phpQuery::newDocument ($data);

    $as = pq (".btn-group.btn-group-paging > .btn", $query);
    $divs = pq (".r-list-container.action-bar-margin.bbs-screen > div", $query);
    
    $arr = array ();
    for ($i = 0; $i < $divs->length; $i++) { 
      if ($divs->eq ($i)->hasClass ('r-list-sep'))
        break;

      $link = $divs->eq ($i)->find ('.title a');
      if (!(isset ($link) && $link && $link->length && ($title = trim ($link->text ())) && ($href = trim ($link->attr ('href')))))
        break;

      $date = $divs->eq ($i)->find ('.meta .date');
      $author = $divs->eq ($i)->find ('.meta .author');

      if (!(isset ($date) && $date && $date->length && ($date = trim ($date->text ())) && isset ($author) && $author && $author->length && ($author = trim ($author->text ()))))
        break;

      $count = $divs->eq ($i)->find ('.hl');
      $count = isset ($count) && $count && $count->length && ($count = trim ($count->text ())) ? $count : '0';

      array_push ($arr, array (
          'pid' => pathinfo ($link->attr ('href'), PATHINFO_BASENAME),
          'count' => $count,
          'title' => $title,
          'link' => $href,
          'date' => $date,
          'author' => $author,
        ));
    }
    return array (
        'prev' => $as->length > 2 && !$as->eq (1)->hasClass ('disabled') ? $as->eq (1)->attr ('href') : '',
        'list' => $arr,
        'next' => $as->length > 3 && !$as->eq (2)->hasClass ('disabled') ? $as->eq (2)->attr ('href') : '',
      );
  }

}