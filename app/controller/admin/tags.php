<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2017, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class tags extends RestfulController {
  // public $self_model = 'Tag';

  public function index () {
    
    // $total = Tag::count ();
    // $tags = Tag::find ('all', array ('order' => 'id DESC'));

    // return View::create ('tags/index.php')
    //            ->with ('total', $total)
    //            ->with ('tags', $tags)
    //            ->output ();
  }
  // add
  public function add () {

    return View::create ('tags/add.php')
               ->output ();
  }
  // create
  public function create () {
    $posts = Input::post ();

    $result = Tag::transaction (function () use ($posts) {
      return Tag::create ($posts);
    });
    
    return $result && URL::refresh (Restful::index ());
  }
  public function edit ($id) {
    return View::create ('tags/edit.php')
               ->with ('tag', $this->self)
               ->output ();
  }
  public function update ($id) {
  }
  public function destroy ($id) {
  }
  public function show ($id) {
  }
}
