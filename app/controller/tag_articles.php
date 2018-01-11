<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class tag_articles extends RestfulController {

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
    $where = Where::create ('tag_id = ?', $this->parent->id);

    $total = Article::count ($where);
    
    $page = Pagination::info ($total);
    
    $objs = Article::find ('all', array ('order' => 'id DESC', 'offset' => $page['offset'], 'limit' => $page['limit'], 'where' => $where));

    return $this->view->setPath ('tag_articles/index.php')
                      ->with ('total', $total)
                      ->with ('objs', $objs)
                      ->with ('pagination', $page['links'])
                      ->output ();
  }

  public function add () {
    return $this->view->setPath ('tag_articles/add.php')
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
      return ($obj = Article::create ($posts))
          && $obj->putFiles ($files);
    };

    $posts = Input::post ();
    $files = Input::file ();

    $posts['tag_id'] = $this->parent->id;
    
    if ($error = Validation::form ($validation, $posts, $files))
      return refresh (RestfulUrl::add (), 'flash', array ('type' => 'failure', 'msg' => '失敗！' . $error, 'params' => $posts));

    if ($error = Tag::getTransactionError ($transaction, $posts, $files))
      return refresh (RestfulUrl::add (), 'flash', array ('type' => 'failure', 'msg' => '失敗！' . $error, 'params' => $posts));

    return refresh (RestfulUrl::index (), 'flash', array ('type' => 'success', 'msg' => '成功！', 'params' => array ()));
  }

  public function edit ($obj) {
    return $this->view->setPath ('tag_articles/edit.php')
                      ->with ('obj', $obj)
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
      return $obj->columnsUpdate ($posts)
          && $obj->save ()
          && $obj->putFiles ($files);;
    };

    $posts = Input::post ();
    $files = Input::file ();

    if ($error = Validation::form ($validation, $posts, $files, $obj))
      return refresh (RestfulUrl::edit ($obj), 'flash', array ('type' => 'failure', 'msg' => '失敗！' . $error, 'params' => $posts));

    if ($error = Tag::getTransactionError ($transaction, $posts, $files, $obj))
      return refresh (RestfulUrl::edit ($obj), 'flash', array ('type' => 'failure', 'msg' => '失敗！' . $error, 'params' => $posts));

    return refresh (RestfulUrl::index (), 'flash', array ('type' => 'success', 'msg' => '成功！', 'params' => array ()));
  }

  public function destroy ($obj) {
    if ($error = Article::getTransactionError (function () use ($obj) { return $obj->destroy (); }))
      return refresh (RestfulUrl::index (), 'flash', array ('type' => 'failure', 'msg' => '失敗！' . $error, 'params' => array ()));

    return refresh (RestfulUrl::index (), 'flash', array ('type' => 'success', 'msg' => '成功！', 'params' => array ()));
  }

  public function show ($obj) {
    $content = View::create ('tag_articles/show.php')
                   ->with ('article', $obj)
                   ->get ();

    return View::create ('layout.php')
               ->with ('content', $content)
               ->output ();
  }
}
