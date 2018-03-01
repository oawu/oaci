<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class articles extends AdminRestfulController {

  public function __construct () {
    parent::__construct ();
    $this->layout->with ('current_url', RestfulUrl::url ('admin/articles@index'));
  }

  public function index () {
    $where = Where::create ();

    $search = Restful\Search::create ($where)
                            ->input ('標題', function ($val) { return Where::create ('title LIKE ?', '%' . $val . '%'); }, 'text');

    $total = Article::count ($where);
    $page  = Pagination::info ($total);
    $objs  = Article::find ('all', array (
               'order' => Restful\Order::desc ('id'),
               'offset' => $page['offset'],
               'limit' => $page['limit'],
               'where' => $where));

    $search->setObjs ($objs)
           ->setTotal ($total)
           ->setAddUrl (RestfulUrl::add ());

    return $this->view->setPath ('admin/articles/index.php')
                      ->with ('search', $search)
                      ->with ('pagination', implode ('', $page['links']));
  }

  public function add () {
    return $this->view->setPath ('admin/articles/add.php');
  }

  public function create () {
    $validation = function (&$posts, $files) {
      // Validation::need ($posts, 'title', '標題')->isStringOrNumber ()->doTrim ()->doRemoveHtmlTags ()->length (1, 255);
      // Validation::need ($posts, 'content', '內容')->isStringOrNumber ()->doTrim ();
      // Validation::need ($files, 'cover', '封面')->isUploadFile ()->formats ('jpg', 'gif', 'png')->size (1, 10 * 1024 * 1024);
      // Validation::need ($files, 'images', '其他照片')->isArray ()->fileterIsUploadFiles ()->filterFormats ('jpg', 'gif', 'png')->filterSize (1, 10 * 1024 * 1024)->count (1);
      // Validation::need ($posts, 'user_id', '作者')->isNumber ()->doTrim ()->greater (0)->doRemoveHtmlTags ()->inArray (User::getArray ('id'));
      // Validation::need ($posts, 'action', '開啟方式', Article::ACTION_TARGET)->isStringOrNumber ()->doTrim ()->doRemoveHtmlTags ()->inArray (array_keys (Article::$actionTexts));
      // Validation::maybe ($posts, 'status', '狀態', Article::STATUS_OFF)->isStringOrNumber ()->doTrim ()->doRemoveHtmlTags ()->inArray (array_keys (Article::$statusTexts));

      Validation::need ($posts, 'lat', '緯度')->isNumber ()->doTrim ()->greater (-85)->less (85)->doRemoveHtmlTags ();
      Validation::need ($posts, 'lng', '經度')->isNumber ()->doTrim ()->greater (-180)->less (180)->doRemoveHtmlTags ();
    };

    $transaction = function ($posts, $files, &$obj) {
      return ($obj = Article::create ($posts))
           && $obj->putFiles ($files);
    };

    $transaction2 = function ($files, $obj) {
      foreach ($files['images'] as $image)
        if (!(($img = ArticleImage::create (array ('article_id' => $obj->id, 'name' => ''))) && $img->name->put ($image)))
          return false;
      return true;
    };

    $posts = Input::post ();
    $files = Input::file ();
    $files['images'] = Input::file ('images[]');
    
    if ($error = Validation::form ($validation, $posts, $files))
      return refresh (RestfulUrl::add (), 'flash', array ('type' => 'failure', 'msg' => '失敗！' . $error, 'params' => $posts));

    if ($error = Article::getTransactionError ($transaction, $posts, $files, $obj))
      return refresh (RestfulUrl::add (), 'flash', array ('type' => 'failure', 'msg' => '失敗！' . $error, 'params' => $posts));

    if ($error = ArticleImage::getTransactionError ($transaction2, $files, $obj))
      return refresh (RestfulUrl::add (), 'flash', array ('type' => 'failure', 'msg' => '失敗！' . $error, 'params' => $posts));

    return refresh (RestfulUrl::index (), 'flash', array ('type' => 'success', 'msg' => '成功！', 'params' => array ()));
  }

  public function edit ($obj) {
    return $this->view->setPath ('admin/articles/edit.php');
  }

  public function update ($obj) {
    $validation = function (&$posts, $obj) {
      Validation::maybe ($posts, 'name', '名稱')->isStringOrNumber ()->doTrim ()->doRemoveHtmlArticles ()->length (1, 255);
    };

    $transaction = function ($posts, $obj) {
      return $obj->columnsUpdate ($posts)
          && $obj->save ();
    };

    $posts = Input::post ();

    if ($error = Validation::form ($validation, $posts, $obj))
      return refresh (RestfulUrl::edit ($obj), 'flash', array ('type' => 'failure', 'msg' => '失敗！' . $error, 'params' => $posts));

    if ($error = Article::getTransactionError ($transaction, $posts, $obj))
      return refresh (RestfulUrl::edit ($obj), 'flash', array ('type' => 'failure', 'msg' => '失敗！' . $error, 'params' => $posts));

    return refresh (RestfulUrl::index (), 'flash', array ('type' => 'success', 'msg' => '成功！', 'params' => array ()));
  }

  public function destroy ($obj) {
    if ($error = Article::getTransactionError (function ($obj) { return $obj->destroy (); }, $obj))
      return refresh (RestfulUrl::index (), 'flash', array ('type' => 'failure', 'msg' => '失敗！' . $error, 'params' => array ()));

    return refresh (RestfulUrl::index (), 'flash', array ('type' => 'success', 'msg' => '成功！', 'params' => array ()));
  }

  public function show ($obj) {
    return $this->view->setPath ('articles/show.php');
  }
}
