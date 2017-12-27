<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

class URI {
  private static $uriString;
  private static $segments;
  private static $rsegments;
  // public $keyval = array();
  
  // protected $_permitted_uri_chars;

  public static function init () {
    URI::$uriString = '';
    URI::$segments = array ();
    URI::$rsegments = array ();

    if (isCli () || Config::get ('general', 'enable_query_strings') !== true) {
      $uri = isCli() ? self::parseArgv () : self::parseRequestUri ();
      self::setUriString ($uri);
    }
  }

  private static function setUriString ($str) {
    self::$uriString = trim (removeInvisibleCharacters ($str, false), '/');

    if (self::$uriString !== '') {
      if (($suffix = (string)Config::get ('general', 'url_suffix')) !== '')
        if (substr (self::$uriString, -($slen = strlen ($suffix))) === $suffix)
          self::$uriString = substr (self::$uriString, 0, -$slen);

      self::$segments[0] = null;
      
      foreach (explode ('/', trim (self::$uriString, '/')) as $val) {
        $val = trim ($val);

        self::filterUri ($val);

        if ($val !== '')
          array_push (self::$segments, $val);
      }

      unset (self::$segments[0]);
    }
  }

  private static function parseRequestUri () {
    if (!isset ($_SERVER['REQUEST_URI'], $_SERVER['SCRIPT_NAME']))
      return '';

    $uri = parse_url ('http://dummy' . $_SERVER['REQUEST_URI']);
    $query = isset ($uri['query']) ? $uri['query'] : '';
    $uri = isset ($uri['path']) ? $uri['path'] : '';

    if (isset ($_SERVER['SCRIPT_NAME'][0]))
      $uri = strpos ($uri, $_SERVER['SCRIPT_NAME']) === 0 ? (string) substr ($uri, strlen ($_SERVER['SCRIPT_NAME'])) : (strpos ($uri, dirname ($_SERVER['SCRIPT_NAME'])) === 0 ? (string) substr ($uri, strlen (dirname ($_SERVER['SCRIPT_NAME']))) : $uri);

    if (trim ($uri, '/') === '' && strncmp ($query, '/', 1) === 0) {
      $query = explode ('?', $query, 2);
      $uri = $query[0];
      $_SERVER['QUERY_STRING'] = isset ($query[1]) ? $query[1] : '';
    } else {
      $_SERVER['QUERY_STRING'] = $query;
    }

    parse_str ($_SERVER['QUERY_STRING'], $_GET);

    if ($uri === '/' OR $uri === '')
      return '/';

    return self::removeRelativeDirectory ($uri);
  }
  private static function parseArgv () {
    return ($args = array_slice ($_SERVER['argv'], 1)) ? implode ('/', $args) : '';
  }
  private static function removeRelativeDirectory ($uri) {
    $uris = array ();
    $tok = strtok ($uri, '/');

    while ($tok !== false) {
      if ((!empty ($tok) || $tok === '0') && $tok !== '..')
        array_push ($uris, $tok);
      $tok = strtok ('/');
    }

    return implode ('/', $uris);
  }
  public static function filterUri (&$str) {
    $c = Config::get ('general', 'permitted_uri_chars');

    if ($str && $c && !preg_match ('/^[' . $c . ']+$/i' . (UTF8_ENABLED ? 'u' : ''), $str))
      class_exists ('Exceptions') && Exceptions::showError ('網址有不合法的字元！', 400);
  }
  public static function uriString () {
    return self::$uriString;
  }
  public static function segments () {
    return self::$segments;
  }
  public static function rsegments () {
    return self::$rsegments;
  }

  public static function setRsegments ($rsegments) {
    return self::$rsegments = $rsegments;
  }

  // public static function segment ($n, $noResult = null) {
  //   return isset (self::$segments[$n]) ? self::$segments[$n] : $noResult;
  // }





















  // protected function _parse_query_string()
  // {
  //   $uri = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : @getenv('QUERY_STRING');

  //   if (trim($uri, '/') === '')
  //   {
  //     return '';
  //   }
  //   elseif (strncmp($uri, '/', 1) === 0)
  //   {
  //     $uri = explode('?', $uri, 2);
  //     $_SERVER['QUERY_STRING'] = isset($uri[1]) ? $uri[1] : '';
  //     $uri = $uri[0];
  //   }

  //   parse_str($_SERVER['QUERY_STRING'], $_GET);

  //   return self::removeRelativeDirectory ($uri);
  // }

  // // --------------------------------------------------------------------

  // /**
  //  * Parse CLI arguments
  //  *
  //  * Take each command line argument and assume it is a URI segment.
  //  *
  //  * @return  string
  //  */
  

  // // --------------------------------------------------------------------

  // /**
  //  * Remove relative directory (../) and multi slashes (///)
  //  *
  //  * Do some final cleaning of the URI and return it, currently only used in self::_parse_request_uri()
  //  *
  //  * @param string  $uri
  //  * @return  string
  //  */
  
  // // --------------------------------------------------------------------

  // /**
  //  * Filter URI
  //  *
  //  * Filters segments for malicious characters.
  //  *
  //  * @param string  $str
  //  * @return  void
  //  */

  // // --------------------------------------------------------------------

  // /**
  //  * Fetch URI Segment
  //  *
  //  * @see   CI_URI::$segments
  //  * @param int   $n    Index
  //  * @param mixed   $no_result  What to return if the segment index is not found
  //  * @return  mixed
  //  */

  // // --------------------------------------------------------------------

  // /**
  //  * Fetch URI "routed" Segment
  //  *
  //  * Returns the re-routed URI segment (assuming routing rules are used)
  //  * based on the index provided. If there is no routing, will return
  //  * the same result as CI_URI::segment().
  //  *
  //  * @see   CI_URI::$rsegments
  //  * @see   CI_URI::segment()
  //  * @param int   $n    Index
  //  * @param mixed   $no_result  What to return if the segment index is not found
  //  * @return  mixed
  //  */
  // public function rsegment($n, $no_result = null)
  // {
  //   return isset($this->rsegments[$n]) ? $this->rsegments[$n] : $no_result;
  // }

  // // --------------------------------------------------------------------

  // /**
  //  * URI to assoc
  //  *
  //  * Generates an associative array of URI data starting at the supplied
  //  * segment index. For example, if this is your URI:
  //  *
  //  *  example.com/user/search/name/joe/location/UK/gender/male
  //  *
  //  * You can use this method to generate an array with this prototype:
  //  *
  //  *  array (
  //  *    name => joe
  //  *    location => UK
  //  *    gender => male
  //  *   )
  //  *
  //  * @param int $n    Index (default: 3)
  //  * @param array $default  Default values
  //  * @return  array
  //  */
  // public function uri_to_assoc($n = 3, $default = array())
  // {
  //   return $this->_uri_to_assoc($n, $default, 'segment');
  // }

  // // --------------------------------------------------------------------

  // /**
  //  * Routed URI to assoc
  //  *
  //  * Identical to CI_URI::uri_to_assoc(), only it uses the re-routed
  //  * segment array.
  //  *
  //  * @see   CI_URI::uri_to_assoc()
  //  * @param   int $n    Index (default: 3)
  //  * @param   array $default  Default values
  //  * @return  array
  //  */
  // public function ruri_to_assoc($n = 3, $default = array())
  // {
  //   return $this->_uri_to_assoc($n, $default, 'rsegment');
  // }

  // // --------------------------------------------------------------------

  // /**
  //  * Internal URI-to-assoc
  //  *
  //  * Generates a key/value pair from the URI string or re-routed URI string.
  //  *
  //  * @used-by CI_URI::uri_to_assoc()
  //  * @used-by CI_URI::ruri_to_assoc()
  //  * @param int $n    Index (default: 3)
  //  * @param array $default  Default values
  //  * @param string  $which    Array name ('segment' or 'rsegment')
  //  * @return  array
  //  */
  // protected function _uri_to_assoc($n = 3, $default = array(), $which = 'segment')
  // {
  //   if (!is_numeric($n))
  //   {
  //     return $default;
  //   }

  //   if (isset($this->keyval[$which], $this->keyval[$which][$n]))
  //   {
  //     return $this->keyval[$which][$n];
  //   }

  //   $total_segments = "total_{$which}s";
  //   $segment_array = "{$which}_array";

  //   if ($this->$total_segments() < $n)
  //   {
  //     return (count($default) === 0)
  //       ? array()
  //       : array_fill_keys($default, null);
  //   }

  //   $segments = array_slice($this->$segment_array(), ($n - 1));
  //   $i = 0;
  //   $lastval = '';
  //   $retval = array();
  //   foreach ($segments as $seg)
  //   {
  //     if ($i % 2)
  //     {
  //       $retval[$lastval] = $seg;
  //     }
  //     else
  //     {
  //       $retval[$seg] = null;
  //       $lastval = $seg;
  //     }

  //     $i++;
  //   }

  //   if (count($default) > 0)
  //   {
  //     foreach ($default as $val)
  //     {
  //       if ( ! array_key_exists($val, $retval))
  //       {
  //         $retval[$val] = null;
  //       }
  //     }
  //   }

  //   // Cache the array for reuse
  //   isset($this->keyval[$which]) OR $this->keyval[$which] = array();
  //   $this->keyval[$which][$n] = $retval;
  //   return $retval;
  // }

  // // --------------------------------------------------------------------

  // /**
  //  * Assoc to URI
  //  *
  //  * Generates a URI string from an associative array.
  //  *
  //  * @param array $array  Input array of key/value pairs
  //  * @return  string  URI string
  //  */
  // public function assoc_to_uri($array)
  // {
  //   $temp = array();
  //   foreach ((array) $array as $key => $val)
  //   {
  //     $temp[] = $key;
  //     $temp[] = $val;
  //   }

  //   return implode('/', $temp);
  // }

  // // --------------------------------------------------------------------

  // /**
  //  * Slash segment
  //  *
  //  * Fetches an URI segment with a slash.
  //  *
  //  * @param int $n  Index
  //  * @param string  $where  Where to add the slash ('trailing' or 'leading')
  //  * @return  string
  //  */
  // public function slash_segment($n, $where = 'trailing')
  // {
  //   return $this->_slash_segment($n, $where, 'segment');
  // }

  // // --------------------------------------------------------------------

  // /**
  //  * Slash routed segment
  //  *
  //  * Fetches an URI routed segment with a slash.
  //  *
  //  * @param int $n  Index
  //  * @param string  $where  Where to add the slash ('trailing' or 'leading')
  //  * @return  string
  //  */
  // public function slash_rsegment($n, $where = 'trailing')
  // {
  //   return $this->_slash_segment($n, $where, 'rsegment');
  // }

  // // --------------------------------------------------------------------

  // /**
  //  * Internal Slash segment
  //  *
  //  * Fetches an URI Segment and adds a slash to it.
  //  *
  //  * @used-by CI_URI::slash_segment()
  //  * @used-by CI_URI::slash_rsegment()
  //  *
  //  * @param int $n  Index
  //  * @param string  $where  Where to add the slash ('trailing' or 'leading')
  //  * @param string  $which  Array name ('segment' or 'rsegment')
  //  * @return  string
  //  */
  // protected function _slash_segment($n, $where = 'trailing', $which = 'segment')
  // {
  //   $leading = $trailing = '/';

  //   if ($where === 'trailing')
  //   {
  //     $leading  = '';
  //   }
  //   elseif ($where === 'leading')
  //   {
  //     $trailing = '';
  //   }

  //   return $leading.$this->$which($n).$trailing;
  // }

  // // --------------------------------------------------------------------

  // /**
  //  * Segment Array
  //  *
  //  * @return  array CI_URI::$segments
  //  */
  // public function segment_array()
  // {
  //   return $this->segments;
  // }

  // // --------------------------------------------------------------------

  // /**
  //  * Routed Segment Array
  //  *
  //  * @return  array CI_URI::$rsegments
  //  */
  // public function rsegment_array()
  // {
  //   return $this->rsegments;
  // }

  // // --------------------------------------------------------------------

  // /**
  //  * Total number of segments
  //  *
  //  * @return  int
  //  */
  // public function total_segments()
  // {
  //   return count($this->segments);
  // }

  // // --------------------------------------------------------------------

  // /**
  //  * Total number of routed segments
  //  *
  //  * @return  int
  //  */
  // public function total_rsegments()
  // {
  //   return count($this->rsegments);
  // }

  // // --------------------------------------------------------------------

  // /**
  //  * Fetch URI string
  //  *
  //  * @return  string  CI_URI::$uriString
  //  */
  // // --------------------------------------------------------------------

  // /**
  //  * Fetch Re-routed URI string
  //  *
  //  * @return  string
  //  */
  // public function ruri_string()
  // {
  //   return ltrim(load_class('Router', 'core')->directory, '/').implode('/', $this->rsegments);
  // }

}
