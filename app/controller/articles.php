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
    
    $articles = Article::find ('all', array (
      'order' => 'id DESC',
      'offset' => $pgn['offset'],
      'limit' => $pgn['limit'],
      'where' => $where
      ));

    $flash = Session::getFlashData ('flash');

    $content = View::create ('articles/index.php')
                   ->with ('flash', $flash)
                   ->with ('total', $total)
                   ->with ('parent', $this->parent)
                   ->with ('articles', $articles)
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
      if (!isset ($posts['title']))
        return '請填寫 標題';
      
      if ($error = Validation::create ($posts['title'], '標題')->isStringOrNumber ()->length (1, 255)->getError ())
        return $error;

      if (!isset ($posts['tag_id']))
        return '請選擇 分類';

      if ($error = Validation::create ($posts['tag_id'], '分類')->isNumber ('請選擇')->greater (0)->getError ())
        return $error;

      if (isset ($posts['content']) && ($error = Validation::create ($posts['content'], '內容')->isStringOrNumber ()->length (0)->getError ()))
        return $error;

      if (!isset ($files['cover']))
        return '請選擇 封面';

      if ($error = Validation::create ($files['cover'], '封面')->isUploadFile ()->formats ('jpg', 'gif', 'png')->size (1, 10 * 1024 * 1024)->getError ())
        return $error;

      return '';
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
    
    if ($error = $validation ($posts, $files))
      return refresh (RestfulUrl::add (), 'flash', array ('type' => 'failure', 'msg' => '失敗！' . $error, 'params' => $posts));

    if ($error = Article::getTransactionError ($transaction, $posts, $files))
      return refresh (RestfulUrl::add (), 'flash', array ('type' => 'failure', 'msg' => '失敗！' . $error, 'params' => $posts));

    return refresh (RestfulUrl::index (), 'flash', array ('type' => 'success', 'msg' => '成功！'));
  }
  public function edit ($obj) {
    $flash = Session::getFlashData ('flash');

    $content = View::create ('articles/edit.php')
                   ->with ('article', $obj)
                   ->with ('flash', $flash)
                   ->with ('params', $flash['params'])
                   ->get ();

    return View::create ('layout.php')
               ->with ('content', $content)
               ->output ();
  }
  public function update ($obj) {
    $validation = function ($obj, &$posts, &$files) {
      if (isset ($posts['title']) && ($error = Validation::create ($posts['title'], '標題')->isStringOrNumber ()->length (1, 255)->getError ()))
        return $error;

      if (isset ($posts['tag_id']) && ($error = Validation::create ($posts['tag_id'], '分類')->isNumber ('請選擇')->greater (0)->getError ()))
        return $error;

      if (isset ($posts['content']) && ($error = Validation::create ($posts['content'], '內容')->isStringOrNumber ()->length (0)->getError ()))
        return $error;

      if (!($obj->cover->getValue () || isset ($files['cover'])))
        return '請選擇 封面';

      if (isset ($files['cover']) && ($error = Validation::create ($files['cover'], '封面')->isUploadFile ()->formats ('jpg', 'gif', 'png')->size (1, 10 * 1024 * 1024)->getError ()))
        return $error;

      return '';
    };

    $transaction = function ($obj, $files) {
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

    if ($error = $validation ($obj, $posts, $files))
      return refresh (RestfulUrl::edit ($obj), 'flash', array ('type' => 'failure', 'msg' => '失敗！' . $error, 'params' => $posts));

    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;

    if ($error = Article::getTransactionError ($transaction, $obj, $files))
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
