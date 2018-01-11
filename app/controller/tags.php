<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class tags extends RestfulController {
  public function index () {
    $where = Where::create ('status = ?', Tag::STATUS_ON);
    
    $total = Tag::count ($where);

    $pgn = Pagination::info ($total);

    $objs = Tag::find ('all', array ('order' => 'id DESC', 'offset' => $pgn['offset'], 'limit' => $pgn['limit'], 'include' => array ('articles'), 'where' => $where));

    $flash = Session::getFlashData ('flash');

    $content = View::create ('tags/index.php')
                   ->with ('flash', $flash)
                   ->with ('total', $total)
                   ->with ('objs', $objs)
                   ->with ('pgn', $pgn['links'])
                   ->get ();

    return View::create ('layout.php')
               ->with ('content', $content)
               ->output ();
  }
  public function add () {
    $flash = Session::getFlashData ('flash');

    $content = View::create ('tags/add.php')
                   ->with ('flash', $flash)
                   ->with ('params', $flash['params'])
                   ->get ();

    return View::create ('layout.php')
               ->with ('content', $content)
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

    return refresh (RestfulUrl::index (), 'flash', array ('type' => 'success', 'msg' => '成功！'));
  }
  public function edit ($obj) {
    $flash = Session::getFlashData ('flash');

    $content = View::create ('tags/edit.php')
                   ->with ('obj', $obj)
                   ->with ('flash', $flash)
                   ->with ('params', $flash['params'])
                   ->get ();

    return View::create ('layout.php')
               ->with ('content', $content)
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

    return refresh (RestfulUrl::index (), 'flash', array ('type' => 'success', 'msg' => '成功！'));
  }
  public function destroy ($obj) {
    if ($error = Tag::getTransactionError (function ($obj) { return $obj->destroy (); }, $obj))
      return refresh (RestfulUrl::index (), 'flash', array ('type' => 'failure', 'msg' => '失敗！' . $error));

    return refresh (RestfulUrl::index (), 'flash', array ('type' => 'success', 'msg' => '成功！'));
  }
  public function show ($obj) {
    $content = View::create ('tags/show.php')
                   ->with ('obj', $obj)
                   ->get ();

    return View::create ('layout.php')
               ->with ('content', $content)
               ->output ();
  }
}
