<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class tags extends RestfulController {
  public function index () {
    $where = WhereBuilder::create ('status = ?', Tag::STATUS_1);
    
    $total = Tag::count ($where);

    $pgn = Pagination::info ($total);

    $tags = Tag::find ('all', array (
      'order' => 'id DESC',
      'offset' => $pgn['offset'],
      'limit' => $pgn['limit'],
      'include' => array ('articles'),
      'where' => $where
      ));

    $content = View::create ('admin/tags/index.php')
                   ->with ('total', $total)
                   ->with ('tags', $tags)
                   ->with ('pgn', $pgn['links'])
                   ->get ();

    return View::create ('layout.php')
               ->with ('content', $content)
               ->output ();
  }
  public function add () {
    $content = View::create ('admin/tags/add.php')
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

    if ($error = $validation ($posts))
      return refresh (RestfulUrl::add (), 'result.failure', '失敗！' . $error . '！');

    if ($error = Tag::getTransactionError (function () use ($posts) { return Tag::create ($posts); }))
      return refresh (RestfulUrl::add (), 'result.failure', '失敗！' . $error . '！');

    return refresh (RestfulUrl::index (), 'result.success', '成功！');
  }
  public function edit ($obj) {
    $content = View::create ('admin/tags/edit.php')
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

    if ($error = $validation ($obj, $posts))
      return refresh (RestfulUrl::edit ($obj), 'result.failure', '失敗！' . $error . '！');

    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;

    if ($error = Tag::getTransactionError (function () use ($obj) { return $obj->save (); }))
      return refresh (RestfulUrl::edit ($obj), 'result.failure', '失敗！' . $error . '！');

    return refresh (RestfulUrl::index (), 'result.success', '成功！');
  }
  public function destroy ($obj) {
    if ($error = Tag::getTransactionError (function () use ($obj) { return $obj->destroy (); }))
      return refresh (RestfulUrl::index (), 'result.failure', '失敗！' . $error . '！');

    return refresh (RestfulUrl::index (), 'result.success', '成功！');
  }
  public function show ($obj) {
    $content = View::create ('admin/tags/show.php')
                   ->with ('tag', $obj)
                   ->get ();
    return View::create ('layout.php')
               ->with ('content', $content)
               ->output ();
  }
}
