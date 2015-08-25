<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2015 OA Wu Design
 */

class ElasticaSearch {
  private $CI     = null;
  private static $index = null;
  private static $client = null;

  function __construct () {
    $this->CI =& get_instance ();

    if (!($isUse = Cfg::system ('elastica_search', 'is_enabled')))
      return;

    $this->CI->load->library ('autoload');
    spl_autoload_register (array ('Autoload', '__autoload_elastica'));
  }

  protected static function index () {
    return self::$index ? self::$index : self::$index = Cfg::system ('elastica_search', 'index');
  }
  protected static function client () {
    return self::$client ? self::$client : self::$client = new Elastica_Client (array (
        'host' => Cfg::system ('elastica_search', 'ip'),
        'port' => Cfg::system ('elastica_search', 'port')
      ));
  }



  public static function clean_index () {
    if (!($isUse = Cfg::system ('elastica_search', 'is_enabled')))
      return false;

    $index = self::index ();
    $client = self::client ();

    $index = new Elastica_Index ($client, $index);

    if ($index)
      $index->delete ();

    return true;
  }

  public static function create ($type, $datas = array ()) {
    if (!($isUse = Cfg::system ('elastica_search', 'is_enabled')))
      return false;

    $index = self::index ();
    $client = self::client ();

    $limit = Cfg::system ('elastica_search', 'limit');
    $type = $client->getIndex ($index)
                   ->getType ($type);

    $length = count ($datas);

    for ($offset = 0; $offset < $length; $offset += $limit)
      if ($data = array_filter (array_map (function ($data) { return isset ($data['id']) ? new Elastica_Document ($data['id'], $data) : null; }, array_slice ($datas, $offset, $limit))))
        $type->addDocuments ($data);

    $type->getIndex ()->refresh ();
  }

  public static function delete ($type, $ids = array ()) {
    if (!($isUse = Cfg::system ('elastica_search', 'is_enabled')))
      return false;

    $index = self::index ();
    $client = self::client ();

    return $client->getIndex ($index)
                  ->getType ($type)
                  ->deleteIds ($ids);
  }
  private static function _build_query ($keywords, $option) {
    $bool = null;

    // 加入必要條件
    if ($option['must'])
      foreach ($option['must'] as $field => $values)
        if ($values = !is_array ($values) ? array ($values) : $values)
          foreach ($values as $value) {
            $bool = !$bool ? new Elastica_Query_Bool () : $bool;
            $text = new Elastica_Query_Match ();
            $text->setMatch ($field, $value);
            $bool->addMust ($text);
          }

    // 加入非必要條件
    if ($option['must_not'])
      foreach ($option['must_not'] as $field => $values)
        if ($values = !is_array ($values) ? array ($values) : $values)
          foreach ($values as $value) {
            $bool = !$bool ? new Elastica_Query_Bool () : $bool;
            $text = new Elastica_Query_Match ();
            $text->setMatch ($field, $value);
            $bool->addMustNot ($text);
          }

    if ($option['range'])
      foreach ($option['range'] as $field => $values) {
          $bool = !$bool ? new Elastica_Query_Bool () : $bool;
          $text = new Elastica_Query_Range ();
          $text->addField ($field, $values);
          $bool->addMust ($text);
      }

    if ($keywords && $option['fields'])
      foreach ($option['fields'] as $field)
        foreach ($keywords as $keyword) {
          $bool = !$bool ? new Elastica_Query_Bool () : $bool;
          $text = new Elastica_Query_Match ();
          $text->setMatch ($field, $keyword);
          $bool->addShould ($text);
        }

    $query = $bool ? new Elastica_Query ($bool) : new Elastica_Query ();

    $query->setFields ($option['select'])
          ->setSort ($option['sort'])
          ->setFrom ($option['offset']);

    if ($option['limit'] > 0)
      $query->setSize ($option['limit']);


    if ($option['script_fields'])
      foreach ($option['script_fields'] as $name => $script_field) {
        if (!(isset ($script_field['script']) && $script_field['script'])) continue;

        $script = new Elastica_Script ($script_field['script']);

        if (isset ($script_field['params']) && $script_field['params'])
          $script->setParams ($script_field['params']);

        $query->addScriptField ($name, $script);
      }

    return $query;
  }

  private static function _modify_option (&$option) {

    $option['offset'] = isset ($option['offset']) ? $option['offset'] : 0;
    $option['limit'] = isset ($option['limit']) ? $option['limit'] : 100;
    $option['fields'] = isset ($option['fields']) ? $option['fields'] : array();
    $option['select'] = isset ($option['select']) ? $option['select'] : array('id');
    $option['must'] = isset ($option['must']) ? $option['must'] : array();
    $option['must_not'] = isset ($option['must_not']) ? $option['must_not'] : array();
    $option['range'] = isset ($option['range']) ? $option['range'] : array();
    $option['script_fields'] = isset ($option['script_fields']) ? $option['script_fields'] : array();

    $sort = array ();
    if (isset ($option['sort']))
      foreach ($option['sort'] as $key => $order)
        $sort[$key] = array ('order' => strtolower ($order));

    $option['sort'] = $sort;
  }

  public static function find ($type, $keywords = array (), $option = array ()) {
    if (!($isUse = Cfg::system ('elastica_search', 'is_enabled')))
      return array();

    $keywords = is_string ($keywords) ? array ($keywords) : $keywords;
    self::_modify_option ($option);

    try {
      $client = self::client ();
      $index = self::index ();
      $query = self::_build_query ($keywords, $option);

      $search = new Elastica_Search ($client);
      $search->addIndex ($index);
      $search->addType ($type);
      $result = $search->search ($query);

      return array_filter (array_map (function ($result) use ($option) {
                return array_filter (array_map (function ($t) { return isset ($t[0]) ? $t[0] : null; }, $result->getData ()));
              }, $result->getResults ()));
    } catch (Exception $e) {
      return array ();
    }
  }

  public static function count ($type, $keywords = array (), $option = array ()) {
    if (!($isUse = Cfg::system ('elastica_search', 'is_enabled')))
      return 0;

    $keywords = is_string ($keywords) ? array ($keywords) : $keywords;
    self::_modify_option ($option);
    $option['select'] = array ('id');

    try {
      $client = self::client ();
      $index = self::index ();
      $query = self::_build_query ($keywords, $option);

      $index = new Elastica_Index ($client, $index);
      return $index->getType ($type)
                   ->count ($query);

    } catch (Exception $e) {
      return 0;
    }
  }


  // public se


  // public function add_datas ($type, $datas = array ()) {
  //   if (!$this->client)
  //     return;


  // }

  // private function _get_query ($type, $keywords = array (), $offset = 0, $limit = 100, $sort = array ('_score' => array ('order' => 'desc')), $compare_fields = array (), $include = array (), $exclude = array (), $fields = array ('id')) {
  //   if (verifyObject ($this->client)) {
  //     if (count ($include)) {
  //       foreach ($include as $field => $values) {
  //         if (count ($values = !is_array ($values) ? array ($values) : $values)) {
  //           foreach ($values as $value) {
  //             $bool = isset ($bool) ? $bool : new Elastica_Query_Bool ();
  //             $text = new Elastica_Query_Text ();
  //             $text->setFieldQuery ($field, $value);
  //             $bool->addMust ($text);
  //           }
  //         }
  //       }
  //     }

  //     if (count ($exclude)) {
  //       foreach ($exclude as $field => $values) {
  //         if (count ($values = !is_array ($values) ? array ($values) : $values)) {
  //           foreach ($values as $value) {
  //             $bool = isset ($bool) ? $bool : new Elastica_Query_Bool ();
  //             $text = new Elastica_Query_Text ();
  //             $text->setFieldQuery ($field, $value);
  //             $bool->addMustNot ($text);
  //           }
  //         }
  //       }
  //     }

  //     if ((count ($keywords) > 0) && (count ($compare_fields) > 0)) {
  //       foreach ($compare_fields as $field) {
  //         foreach ($keywords as $keyword) {
  //           $bool = isset ($bool) ? $bool : new Elastica_Query_Bool ();
  //           $text = new Elastica_Query_Text ();
  //           $text->setFieldQuery ($field, $keyword)->setFieldParam ($field, 'operator', 'and')->setFieldType ($field, 'phrase');
  //           $bool->addShould ($text);
  //         }
  //       }
  //     }

  //     $query = isset ($bool) ? new Elastica_Query ($bool) : new Elastica_Query ();

  //     $query->setFields (count ($fields) ? $fields : array ('id'))
  //           ->setSort (count ($sort) ? $sort : array ('_score' => array ('order' => 'desc')))
  //           ->setFrom ($offset);

  //     if ($limit > 0) $query->setSize ($limit);

  //     return $query;
  //   } else { return null; }
  // }

  // public function get_datas ($type, $keywords = array (), $offset = 0, $limit = 100, $sort = array ('_score' => array ('order' => 'desc')), $compare_fields = array (), $include = array (), $exclude = array (), $fields = array ('id')) {
  //   if (verifyObject ($this->client)) {
  //     try {
  //       $fields = count ($fields) ? $fields : array ('id');
  //       $search = new Elastica_Search ($this->client);
  //       $search->addIndex ($this->index);
  //       $search->addType ($this->types[$type]);
  //       $result = $search->search ($this->_get_query ($type, $keywords, $offset, $limit, $sort, $compare_fields, $include, $exclude, $fields));
  //       return array_filter (array_map (function ($result) use ($fields) { return ($data = $result->getData ()) ? (count ($fields) > 1 ? array_combine ($fields, array_map (function ($field) use ($data) { return $data[$field]; }, $fields)) : $data[$fields[0]]) : null; }, $result->getResults ()));
  //     } catch (Exception $e) { return array (); }
  //   } else { return array (); }
  // }
















  public static function create_index () {
    if (!($isUse = Cfg::system ('elastica_search', 'is_enabled')))
      return ;

    $client = self::client ();
    $index = self::index ();

    $index = new Elastica_Index ($client, $index);

    $index->create (array (
      'settings' => array (
          'number_of_shards'   => 5,
          'number_of_replicas' => 1,
        )
    ));
  }

  // public static function type () {
  //   if (!($isUse = Cfg::system ('elastica_search', 'is_enabled')))
  //     return array();

  //   $client = self::client ();
  //   $index = self::index ();

  //   $index = new Elastica_Index ($client, $index);
  //   echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
  //   var_dump ($index->getMapping ());
  //   exit ();
  // }




  public function rebuild_index () {
    if (!($this->client && $this->index))
      return;

    $index = new Elastica_Index ($this->client, $this->index);

    // $index->delete ();

    $index->create (array (
      'settings' => array (
          'number_of_shards'   => 5,
          'number_of_replicas' => 1,
        )
    ));
  }

  public function rebuild_type ($type_name) {
    if (verifyObject ($this->client)) {
      $mapping = config ('search_config', 'mappings', $this->types[$type_name]);

      $index = new Elastica_Index ($this->client,$this->index);
      $type = $index->getType ($this->types[$type_name]);
      $type->delete ();

      $index = new Elastica_Index ($this->client, $this->index);
      $type = $index->getType ($this->types[$type_name]);
      $type->setMapping ($mapping);
    }
  }



  public function get_datas_count ($type, $keywords = array (), $offset = 0, $limit = 100, $sort = array ('_score' => array ('order' => 'desc')), $compare_fields = array (), $include = array (), $exclude = array ()) {
    if (verifyObject ($this->client)) {
      $index = new Elastica_Index ($this->client, $this->index);
      $result = $index->getType ($this->types[$type])->count ($this->_get_query ($type, $keywords, $offset, $limit, $sort, $compare_fields, $include, $exclude, $fields = array ('id')));
      return $result;
    } else { return 0; }
  }

  // public function count ($type, $keyword) {
  //   if (verifyObject ($this->client)) {
  //     $index = new Elastica_Index ($this->client, $this->index);
  //     $result = $index->getType ($this->types[$type])->count ($keyword);
  //     return $result;
  //   } else { return 0; }
  // }

  public function get_tokens_array ($text) {
    try {
      if (is_array ($text)) $text = implode (' ', $text);
      $text = rawurlencode (strtolower ($text));

      $CURL = curl_init ();
      curl_setopt ($CURL, CURLOPT_URL, 'http://search.fashionguide.com.tw/fgapi/search/get_tokens/?t=' . $text . '&a=ik');
      curl_setopt ($CURL, CURLOPT_RETURNTRANSFER, true);
      curl_setopt ($CURL, CURLOPT_CONNECTTIMEOUT, 10);
      $response = curl_exec ($CURL);
      $response = preg_match ('/Severity: Warning/', $response) ? array () : array_unique (array_filter (array_map (function ($value) { return strlen (trim ($value)) ? trim ($value) : null; }, explode (',', str_replace ('p2q', '', $response)))));

    } catch (Exception $e) { $response = array (); }
    return $response;
  }

}
