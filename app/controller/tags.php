<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2017, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class tags extends RestfulController {
  public function index () {
    
    $total = Tag::count ();
    $tags = Tag::find ('all', array ('order' => 'id DESC'));

    $content = View::create ('tags/index.php')
                   ->with ('total', $total)
                   ->with ('tags', $tags)
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

    $result = Tag::transaction (function () use ($posts) {
      return Tag::create ($posts);
    });
    
    return $result && URL::refresh (Restful::index ());
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

    return $result && URL::refresh (Restful::index ());
  }
  public function destroy ($obj) {
    $result = Tag::transaction (function () use ($obj) {
      return $obj->destroy ();
    });

    return $result && URL::refresh (Restful::index ());
  }
  public function show ($obj) {
    return View::create ('layout.php')
               ->with ('tag', $obj)
               ->output ();
  }
}
