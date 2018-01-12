<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class tags extends RestfulController {

  public function __construct () {
    parent::__construct ();

    $flash = Session::getFlashData ('flash');
    
    $layout = View::create ('layout.php')
                  ->with ('current_url', RestfulUrl::url ('tags@index'));

    $this->view->appendTo ($layout, 'content')
               ->with ('flash', $flash)
               ->with ('params', $flash['params']);
  }

  public function index () {
    $where = Where::create ('status = ?', Tag::STATUS_ON);
    
    $total = Tag::count ($where);

    $page = Pagination::info ($total);

    $objs = Tag::find ('all', array ('order' => 'id DESC', 'offset' => $page['offset'], 'limit' => $page['limit'], 'include' => array ('articles'), 'where' => $where));

    return $this->view->setPath ('tags/index.php')
                      ->with ('total', $total)
                      ->with ('objs', $objs)
                      ->with ('pagination', $page['links'])
                      ->output ();
  }

  public function add () {
    return $this->view->setPath ('tags/add.php')
                      ->output ();
  }

  public function create () {
    $validation = function (&$posts) {
      Validation::need ($posts, 'name', '名稱')->isStringOrNumber ()->doTrim ()->length (1, 255);
      Validation::need ($posts, 'status', '狀態')->isStringOrNumber ()->doTrim ()->inArray (array_keys (Tag::$statusNames));
    };

    $transaction = function ($posts) {
      return Tag::create ($posts);
    };

    $posts = Input::post ();
    $posts['status'] = Tag::STATUS_ON;

    if ($error = Validation::form ($validation, $posts))
      return refresh (RestfulUrl::add (), 'flash', array ('type' => 'failure', 'msg' => '失敗！' . $error, 'params' => $posts));

    if ($error = Tag::getTransactionError ($transaction, $posts))
      return refresh (RestfulUrl::add (), 'flash', array ('type' => 'failure', 'msg' => '失敗！' . $error, 'params' => $posts));

    return refresh (RestfulUrl::index (), 'flash', array ('type' => 'success', 'msg' => '成功！', 'params' => array ()));
  }

  public function edit ($obj) {
    return $this->view->setPath ('tags/edit.php')
                      ->with ('obj', $obj)
                      ->output ();
  }

  public function update ($obj) {
    $validation = function (&$posts) {
      Validation::maybe ($posts, 'name', '名稱')->isStringOrNumber ()->doTrim ()->length (1, 255);
      Validation::maybe ($posts, 'status', '狀態')->isStringOrNumber ()->doTrim ()->inArray (array_keys (Tag::$statusNames));
    };

    $transaction = function ($posts, $obj) {
      return $obj->columnsUpdate ($posts) && $obj->save ();
    };

    $posts = Input::post ();

    if ($error = Validation::form ($validation, $posts))
      return refresh (RestfulUrl::edit ($obj), 'flash', array ('type' => 'failure', 'msg' => '失敗！' . $error, 'params' => $posts));

    if ($error = Tag::getTransactionError ($transaction, $posts, $obj))
      return refresh (RestfulUrl::edit ($obj), 'flash', array ('type' => 'failure', 'msg' => '失敗！' . $error, 'params' => $posts));

    return refresh (RestfulUrl::index (), 'flash', array ('type' => 'success', 'msg' => '成功！', 'params' => array ()));
  }

  public function destroy ($obj) {
    if ($error = Tag::getTransactionError (function ($obj) { return $obj->destroy (); }, $obj))
      return refresh (RestfulUrl::index (), 'flash', array ('type' => 'failure', 'msg' => '失敗！' . $error, 'params' => array ()));

    return refresh (RestfulUrl::index (), 'flash', array ('type' => 'success', 'msg' => '成功！', 'params' => array ()));
  }

  public function show ($obj) {
    return $this->view->setPath ('tags/show.php')
                      ->with ('obj', $obj)
                      ->output ();
  }
}