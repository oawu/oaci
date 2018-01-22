<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class banners extends AdminRestfulController {

  public function __construct () {
    parent::__construct ();
    $this->layout->with ('current_url', RestfulUrl::url ('admin/banners@index'));
  }

  public function index () {
    $where = Where::create ();

    $search = AdminSearch::create ($where)
                         ->input ('標題', function ($val) { return Where::create ('title LIKE ?', '%' . $val . '%'); }, 'text')
                         ->select ('狀態', 'status = ?', array_map (function ($key) { return array ('text' => Banner::$statusTexts[$key], 'value' => $key); }, array_keys (Banner::$statusTexts)))
                         ->checkboxs ('狀態', 'status IN (?)', array_map (function ($key) { return array ('text' => Banner::$statusTexts[$key], 'value' => $key); }, array_keys (Banner::$statusTexts)))
                         ->radios ('狀態', 'status = ?', array_map (function ($key) { return array ('text' => Banner::$statusTexts[$key], 'value' => $key); }, array_keys (Banner::$statusTexts)));

    $total = Banner::count ($where);

    $page = Pagination::info ($total);

    $objs = Banner::find ('all', array ('order' => AdminOrder::desc ('sort'),'offset' => $page['offset'],'limit' => $page['limit'],'where' => $where));

    $table = AdminTableListColumn::create ($objs,
              AdminTableListColumn::create ('啟用')->setWidth (60)->setClass ('center')->setTd (function ($obj, $column) { return $column->setSwitch ($obj->status == Banner::STATUS_ON, array ('class' => 'switch ajax', 'data-column' => 'status', 'data-url' => RestfulUrl::url ('admin/banners@status', $obj), 'data-true' => Banner::STATUS_ON, 'data-false' => Banner::STATUS_OFF, 'data-cntlabel' => 'aaa')); }),
              AdminTableListColumn::create ('ID')->setWidth (50)->setSort ('id')->setTd (function ($obj) { return $obj->id; }),
              AdminTableListColumn::create ('封面')->setWidth (50)->setClass ('oaips')->setTd (function ($obj) { return $obj->cover->toImageTag ('w100'); }),
              AdminTableListColumn::create ('標題')->setWidth (150)->setSort ('title')->setTd (function ($obj) { return $obj->title; }),
              AdminTableListColumn::create ('內容')->setTd (function ($obj) { return $obj->min_column ('content', 100); }),
              AdminTableListColumn::create ('鏈結')->setWidth (150)->setTd (function ($obj) { return $obj->link; }),
              AdminTableListColumn::create ('開啟方式')->setWidth (70)->setTd (function ($obj) { return $obj->link ? Banner::$linkActionTexts[$obj->link_action] : ''; }),
              AdminTableListEditColumn::create ('編輯')->setTd (function ($obj, $column) { return $column->addDeleteLink (RestfulUrl::destroy ($obj))->addEditLink (RestfulUrl::edit ($obj))->addShowLink (RestfulUrl::show ($obj)); }))
            ->setSortUrl (RestfulUrl::url ('admin/banners@sorts'));

    $search = $search->renderForm ($total, RestfulUrl::add (), $table);

    return $this->view->setPath ('admin/banners/index.php')
                      ->with ('search', $search)
                      ->with ('table', $table)
                      ->with ('pagination', implode ('', $page['links']));
  }

  public function add () {
    return $this->view->setPath ('admin/banners/add.php');
  }

  public function create () {
    $validation = function (&$posts, &$files) {
      Validation::maybe ($posts, 'status', '狀態', Banner::STATUS_OFF)->isStringOrNumber ()->doTrim ()->doRemoveHtmloTags ()->inArray (array_keys (Banner::$statusTexts));
      Validation::need ($posts, 'title', '標題')->isStringOrNumber ()->doTrim ()->doRemoveHtmloTags ()->length (1, 255);
      Validation::need ($files, 'cover', '封面')->isUploadFile ()->formats ('jpg', 'gif', 'png')->size (1, 10 * 1024 * 1024);
      Validation::maybe ($posts, 'content', '內容', '')->isStringOrNumber ()->doTrim ();
      Validation::maybe ($posts, 'link', '鏈結', '')->isStringOrNumber ()->doTrim ()->doRemoveHtmloTags ();
      Validation::maybe ($posts, 'link_action', '鏈結開啟方式', Banner::LINK_ACTION_TARGET)->isStringOrNumber ()->doTrim ()->doRemoveHtmloTags ()->inArray (array_keys (Banner::$linkActionTexts));
    };

    $transaction = function ($posts, $files) {
      return ($obj = Banner::create ($posts))
          && $obj->putFiles ($files);
    };

    $posts = Input::post ();
    $posts['sort'] = Banner::count ();
    $posts['content'] = Input::post ('content', false);
    $files = Input::file ();

    if ($error = Validation::form ($validation, $posts, $files))
      return refresh (RestfulUrl::add (), 'flash', array ('type' => 'failure', 'msg' => '失敗！' . $error, 'params' => $posts));

    if ($error = Banner::getTransactionError ($transaction, $posts, $files))
      return refresh (RestfulUrl::add (), 'flash', array ('type' => 'failure', 'msg' => '失敗！' . $error, 'params' => $posts));

    return refresh (RestfulUrl::index (), 'flash', array ('type' => 'success', 'msg' => '成功！', 'params' => array ()));
  }

  public function edit ($obj) {
    return $this->view->setPath ('admin/banners/edit.php');
  }

  public function update ($obj) {
    $validation = function (&$posts, &$files, $obj) {
      Validation::maybe ($posts, 'status', '狀態', Banner::STATUS_OFF)->isStringOrNumber ()->doTrim ()->doRemoveHtmloTags ()->inArray (array_keys (Banner::$statusTexts));
      Validation::need ($posts, 'title', '標題')->isStringOrNumber ()->doTrim ()->doRemoveHtmloTags ()->length (1, 255);
      Validation::maybe ($posts, 'content', '內容', '')->isStringOrNumber ()->doTrim ();
      Validation::maybe ($posts, 'link', '鏈結', '')->isStringOrNumber ()->doTrim ()->doRemoveHtmloTags ();
      Validation::maybe ($posts, 'link_action', '鏈結開啟方式', Banner::LINK_ACTION_TARGET)->isStringOrNumber ()->doTrim ()->doRemoveHtmloTags ()->inArray (array_keys (Banner::$linkActionTexts));
      
      if (!$obj->cover->getValue ())
        Validation::need ($files, 'cover', '封面')->isUploadFile ()->formats ('jpg', 'gif', 'png')->size (1, 10 * 1024 * 1024);
      else
        Validation::maybe ($files, 'cover', '封面')->isUploadFile ()->formats ('jpg', 'gif', 'png')->size (1, 10 * 1024 * 1024);
    };

    $transaction = function ($posts, $files, $obj) {
      return $obj->columnsUpdate ($posts)
          && $obj->save ()
          && $obj->putFiles ($files);
    };

    $posts = Input::post ();
    $posts['content'] = Input::post ('content', false);
    $files = Input::file ();

    if ($error = Validation::form ($validation, $posts, $files, $obj))
      return refresh (RestfulUrl::edit ($obj), 'flash', array ('type' => 'failure', 'msg' => '失敗！' . $error, 'params' => $posts));

    if ($error = Banner::getTransactionError ($transaction, $posts, $files, $obj))
      return refresh (RestfulUrl::edit ($obj), 'flash', array ('type' => 'failure', 'msg' => '失敗！' . $error, 'params' => $posts));

    return refresh (RestfulUrl::index (), 'flash', array ('type' => 'success', 'msg' => '成功！', 'params' => array ()));
  }

  public function destroy ($obj) {
    if ($error = Banner::getTransactionError (function ($obj) { return $obj->destroy (); }, $obj))
      return refresh (RestfulUrl::index (), 'flash', array ('type' => 'failure', 'msg' => '失敗！' . $error, 'params' => array ()));

    return refresh (RestfulUrl::index (), 'flash', array ('type' => 'success', 'msg' => '成功！', 'params' => array ()));
  }

  public function show ($obj) {
    return $this->view->setPath ('banners/show.php');
  }

  public function status ($obj) {
    $validation = function (&$posts) {
      Validation::maybe ($posts, 'status', '狀態', Banner::STATUS_OFF)->isStringOrNumber ()->doTrim ()->doRemoveHtmloTags ()->inArray (array_keys (Banner::$statusTexts));
    };

    $transaction = function ($posts, $obj) {
      return $obj->columnsUpdate ($posts)
          && $obj->save ();
    };

    $posts = Input::post ();

    if ($error = Validation::form ($validation, $posts))
      return Output::json ($error, 400);

    if ($error = Banner::getTransactionError ($transaction, $posts, $obj))
      return Output::json ($error, 400);

    return Output::json (array (
        'status' => $obj->status
      ));
  }
  public function sorts () {
    $validation = function (&$posts) {
      Validation::maybe ($posts, 'changes', '狀態', array ())->isArray ()->doArrayValues ()->doArrayMap (function ($t) {
        if (!isset ($t['id'], $t['ori'], $t['now'])) return Validation::error ('格式不正確(1)');
        if (!$banner = Banner::find ('one', array ('select' => 'id,sort', 'where' => Where::create ('id = ? AND sort = ?', $t['id'], $t['ori'])))) return Validation::error ('格式不正確(2)');
        return array ('obj' => $banner, 'sort' => $t['now']);
      })->doArrayFilter ();
    };

    $posts = Input::post ();

    if ($error = Validation::form ($validation, $posts))
      return Output::json ($error, 400);

    $transaction = function ($posts) {
      foreach ($posts['changes'] as $value)
        $value['obj']->sort = $value['sort'];
      
      foreach ($posts['changes'] as $value)
        if (!$value['obj']->save ())
          return false;

      return true;
    };

    if ($error = Banner::getTransactionError ($transaction, $posts))
      return Output::json ($error, 400);

    return Output::json (array_map (function ($t) {
      return array ('id' => $t->id, 'sort' => $t->sort);
    }, array_column ($posts['changes'], 'obj')));
  }
}
