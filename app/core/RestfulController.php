<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2017, OACI
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

    $this->parents = array_map (function ($param) { return class_exists ($class = $param[0]) ? $class::find_by_id ($param[1]) : gg ('Router Restful Model 設置錯誤，Model：' . $class); }, Router::$router['params']);
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
