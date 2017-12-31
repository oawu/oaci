<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

interface RestfulControllerInterface {
  public function index ();
  public function add ();
  public function create ();
  public function edit ($obj);
  public function update ($obj);
  public function destroy ($obj);
  public function show ($obj);
}

abstract class RestfulController extends Controller implements RestfulControllerInterface {
  public $obj = null;
  public $parents = array ();

  public function __construct () {
    parent::__construct ();
    
    Router::$router || gg ('請設定正確的 Router Restful.');

    $this->parents = array_map (function ($param) {
      $where = WhereBuilder::create ();
      is_numeric ($param[1]) ? $where->and ('id = ?', $param[1]) : gg ('ID 資訊錯誤！');

      if (is_string ($param[0]) && class_exists ($class = $param[0]))
        return $class::find ('one', array ('where' => $where->toArray ()));

      if (is_array ($param[0]) && isset ($param[0]['model']) && class_exists ($class = $param[0]['model'])) {
        isset ($param[0]['where']) && $where->and ($param[0]['where']);
        unset ($param[0]['model'], $param[0]['where']);
        return $class::find ('one', array_merge ($param[0], array ('where' => $where->toArray ())));
      }

      gg ('Router Restful Model 設置錯誤，Model：' . $class);
    }, Router::$router['params']);
    $this->obj = array_pop ($this->parents);

    Restful::setUrls (implode('/', Router::$router['group']), $this->parents);

    Load::sysLib ('Pagination.php', true);
  }
  
  public function _remap ($name, $params) {
    if (!in_array ($name, array ('edit', 'update', 'destroy', 'show')))
      return call_user_func_array (array ($this, $name), $this->parents);
    
    return $this->obj ? call_user_func_array (array ($this, $name), array ($this->obj)) : gg ('找不到該物件！');
  }
}
