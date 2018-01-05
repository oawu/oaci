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
    $posts = Input::post ();
    $posts['status'] = Tag::STATUS_1;

    $result = Tag::transaction (function () use ($posts) {
      return Tag::create ($posts);
    });

    $result ? Session::setFlashData ('result.success', '成功！') : Session::setFlashData ('result.failure', '失敗！');
    
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
    $posts = Input::post ();
    
    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;

    $result = Tag::transaction (function () use ($obj) {
      return $obj->save ();
    });

    $result ? Session::setFlashData ('result.success', '成功！') : Session::setFlashData ('result.failure', '失敗！');
    
    return URL::refresh (RestfulUrl::index ());
  }
  public function destroy ($obj) {
    $result = Tag::transaction (function () use ($obj) {
      return $obj->destroy ();
    });

    $result ? Session::setFlashData ('result.success', '成功！') : Session::setFlashData ('result.failure', '失敗！');
    
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
