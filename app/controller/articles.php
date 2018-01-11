<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class articles extends RestfulController {
  public function index () {
    $where = Where::create ();

    $total = Article::count ($where);
    
    $pgn = Pagination::info ($total);
    
    $objs = Article::find ('all', array ('order' => 'id DESC', 'offset' => $pgn['offset'], 'limit' => $pgn['limit'], 'where' => $where));

    $flash = Session::getFlashData ('flash');

    $content = View::create ('articles/index.php')
                   ->with ('flash', $flash)
                   ->with ('total', $total)
                   ->with ('parent', $this->parent)
                   ->with ('objs', $objs)
                   ->with ('pgn', $pgn['links'])
                   ->get ();

    return View::create ('layout.php')
               ->with ('content', $content)
               ->output ();
  }
  public function add () {
    $flash = Session::getFlashData ('flash');
    
    $content = View::create ('articles/add.php')
                   ->with ('flash', $flash)
                   ->with ('params', $flash['params'])
                   ->get ();

    return View::create ('layout.php')
               ->with ('content', $content)
               ->output ();
  }
  public function create () {
    $validation = function (&$posts, &$files) {
      Validation::need ($posts, 'title', '標題')->isStringOrNumber ()->doTrim ()->length (1, 255);
      Validation::need ($posts, 'tag_id', '分類')->isNumber ('請選擇')->doTrim ()->greater (0);
      Validation::maybe ($posts, 'content', '內容', '')->isStringOrNumber ()->doTrim ()->length (0);
      Validation::need ($files, 'cover', '封面')->isUploadFile ()->formats ('jpg', 'gif', 'png')->size (1, 10 * 1024 * 1024);
    };

    $transaction = function ($posts, $files) {
      if (!$obj = Article::create ($posts))
        return false;

      foreach ($files as $key => $file)
        if (isset ($files[$key]) && $files[$key] && $obj->$key instanceof Uploader)
          if (!$obj->$key->put ($files[$key]))
            return false;
      
      return true;
    };

    $posts = Input::post ();
    $files = Input::file ();
    
    if ($error = Validation::form ($validation, $posts, $files))
      return refresh (RestfulUrl::add (), 'flash', array ('type' => 'failure', 'msg' => '失敗！' . $error, 'params' => $posts));

    if ($error = Tag::getTransactionError ($transaction, $posts, $files))
      return refresh (RestfulUrl::add (), 'flash', array ('type' => 'failure', 'msg' => '失敗！' . $error, 'params' => $posts));

    return refresh (RestfulUrl::index (), 'flash', array ('type' => 'success', 'msg' => '成功！'));
  }
  public function edit ($obj) {
    $flash = Session::getFlashData ('flash');

    $content = View::create ('articles/edit.php')
                   ->with ('obj', $obj)
                   ->with ('flash', $flash)
                   ->with ('params', $flash['params'])
                   ->get ();

    return View::create ('layout.php')
               ->with ('content', $content)
               ->output ();
  }
  public function update ($obj) {
    $validation = function (&$posts, &$files, $obj) {
      Validation::maybe ($posts, 'title', '標題')->isStringOrNumber ()->doTrim ()->length (1, 255);
      Validation::maybe ($posts, 'tag_id', '分類')->isNumber ('請選擇')->doTrim ()->greater (0);
      Validation::maybe ($posts, 'content', '內容', '')->isStringOrNumber ()->doTrim ()->length (0);

      if (!$obj->cover->getValue ())
        Validation::need ($files, 'cover', '封面')->isUploadFile ()->formats ('jpg', 'gif', 'png')->size (1, 10 * 1024 * 1024);
      else
        Validation::maybe ($files, 'cover', '封面')->isUploadFile ()->formats ('jpg', 'gif', 'png')->size (1, 10 * 1024 * 1024);

      return '';
    };

    $transaction = function ($posts, $files, $obj) {
      $obj->columnsUpdate ($posts);

      if (!$obj->save ())
        return false;

      foreach ($files as $key => $file)
        if (isset ($files[$key]) && $files[$key] && $obj->$key instanceof Uploader)
          if (!$obj->$key->put ($files[$key]))
            return false;
      
      return true;
    };

    $posts = Input::post ();
    $files = Input::file ();

    if ($error = Validation::form ($validation, $posts, $files, $obj))
      return refresh (RestfulUrl::edit ($obj), 'flash', array ('type' => 'failure', 'msg' => '失敗！' . $error, 'params' => $posts));

    if ($error = Tag::getTransactionError ($transaction, $posts, $files, $obj))
      return refresh (RestfulUrl::edit ($obj), 'flash', array ('type' => 'failure', 'msg' => '失敗！' . $error, 'params' => $posts));

    return refresh (RestfulUrl::index (), 'flash', array ('type' => 'success', 'msg' => '成功！'));
  }
  public function destroy ($obj) {
    if ($error = Article::getTransactionError (function () use ($obj) { return $obj->destroy (); }))
      return refresh (RestfulUrl::index (), 'flash', array ('type' => 'failure', 'msg' => '失敗！' . $error));

    return refresh (RestfulUrl::index (), 'flash', array ('type' => 'success', 'msg' => '成功！'));
  }
  public function show ($obj) {
    $content = View::create ('articles/show.php')
                   ->with ('article', $obj)
                   ->get ();

    return View::create ('layout.php')
               ->with ('content', $content)
               ->output ();
  }
}
