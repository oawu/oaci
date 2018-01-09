<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class tags extends RestfulController {
  public function index () {
    $total = Tag::count ();

    $pgn = Pagination::info ($total);

    $tags = Tag::find ('all', array (
      'order' => 'id DESC',
      'offset' => $pgn['offset'],
      'limit' => $pgn['limit'],
      'include' => array ('articles'),
      'where' => array ('status = ?', Tag::STATUS_1)
      ));

    $content = View::create ('tags/index.php')
                   ->with ('total', $total)
                   ->with ('tags', $tags)
                   ->with ('pgn', $pgn['links'])
                   ->get ();

    return View::create ('layout.php')
               ->with ('content', $content)
               ->output ();
  }
  public function add () {
    $content = View::create ('tags/add.php')
                   ->get ();

    return View::create ('layout.php')
               ->with ('content', $content)
               ->output ();
  }
  public function create () {
    $validation = function (&$posts) {
      $error = '';

      $error || isset ($posts['name']) || $error = '參數錯誤！';
      $error || $error = Validation::create ($posts['name'], '名稱')->isStringOrNumber ()->length (1, 255)->getError ();

      $error || isset ($posts['status']) || $error = '參數錯誤！';
      $error || $error = Validation::create ($posts['status'], '狀態')->isNumber ()->inArray (array_keys (Tag::$statusNames))->getError ();

      return $error;
    };

    $posts = Input::post ();
    $posts['status'] = Tag::STATUS_1;

    if ($error = $validation ($posts)) {
      Session::setFlashData ('result.failure', '失敗！' . $error . '！');
      return URL::refresh (RestfulUrl::add ());
    }

    if ($error = Tag::getTransactionError (function () use ($posts) {
      return Tag::create ($posts);
    })) {
      Session::setFlashData ('result.failure', '失敗！' . $error . '！');
      return URL::refresh (RestfulUrl::add ());
    }

    Session::setFlashData ('result.success', '成功！');
    return URL::refresh (RestfulUrl::index ());
  }
  public function edit ($obj) {
    $content = View::create ('tags/edit.php')
                   ->with ('tag', $obj)
                   ->get ();

    return View::create ('layout.php')
               ->with ('content', $content)
               ->output ();
  }
  public function update ($obj) {
    $validation = function ($obj, &$posts) {
      $error = '';

      $error || isset ($posts['name']) && $error = Validation::create ($posts['name'], '名稱')->isStringOrNumber ()->length (1, 255)->getError ();
      $error || isset ($posts['status']) && $error = Validation::create ($posts['status'], '狀態')->isNumber ()->inArray (array_keys (Tag::$statusNames))->getError ();

      return $error;
    };

    $posts = Input::post ();

    if ($error = $validation ($obj, $posts)) {
      Session::setFlashData ('result.failure', '失敗！' . $error . '！');
      return URL::refresh (RestfulUrl::edit ($obj));
    }

    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;

    if ($error = Tag::getTransactionError (function () use ($obj) {
      return $obj->save ();
    })) {
      Session::setFlashData ('result.failure', '失敗！' . $error . '！');
      return URL::refresh (RestfulUrl::edit ($obj));
    }

    Session::setFlashData ('result.success', '成功！');
    return URL::refresh (RestfulUrl::index ());
  }
  public function destroy ($obj) {
    if ($error = Tag::getTransactionError (function () use ($obj) {
      return $obj->destroy ();
    })) {
      Session::setFlashData ('result.failure', '失敗！' . $error . '！');
      return URL::refresh (RestfulUrl::index ());
    }

    Session::setFlashData ('result.success', '成功！');
    return URL::refresh (RestfulUrl::index ());
  }
  public function show ($obj) {
    $content = View::create ('tags/show.php')
                   ->with ('tag', $obj)
                   ->get ();
    return View::create ('layout.php')
               ->with ('content', $content)
               ->output ();
  }
}
