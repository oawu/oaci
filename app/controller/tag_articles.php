<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class tag_articles extends RestfulController {
  public function index () {
    $total = Article::count ();
    
    $pgn = Pagination::info ($total);
    
    $tags = Article::find ('all', array (
      'order' => 'id DESC',
      'offset' => $pgn['offset'],
      'limit' => $pgn['limit'],
      'where' => array ('tag_id = ?', $this->parent->id)
      ));

    $content = View::create ('tag_articles/index.php')
                   ->with ('total', $total)
                   ->with ('tags', $tags)
                   ->with ('pgn', $pgn['links'])
                   ->get ();

    return View::create ('layout.php')
               ->with ('content', $content)
               ->output ();
  }
  public function add () {
    $content = View::create ('tag_articles/add.php')
                   ->get ();

    return View::create ('layout.php')
               ->with ('content', $content)
               ->output ();
  }
  public function create () {
    $posts = Input::post ();
    $posts['tag_id'] = $this->parent->id;
    $files = Input::file ();

    $result = Article::transaction (function () use ($posts, $files) {
      if (!$obj = Article::create ($posts))
        return false;

      foreach ($files as $key => $file)
        if (isset ($files[$key]) && $files[$key] && $obj->$key instanceof Uploader)
          if (!$obj->$key->put ($files[$key]))
            return false;
      
      return true;
    });

    $result ? Session::setFlashData ('result.success', '成功！') : Session::setFlashData ('result.failure', '失敗！');

    return URL::refresh (RestfulUrl::index ());
  }
  public function edit ($obj) {
    $content = View::create ('tag_articles/edit.php')
                   ->with ('article', $obj)
                   ->get ();

    return View::create ('layout.php')
               ->with ('content', $content)
               ->output ();
  }
  public function update ($obj) {
    $posts = Input::post ();
    $files = Input::file ();
    
    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;


    $result = Article::transaction (function () use ($obj, $files) {
      if (!$obj->save ())
        return false;

      foreach ($files as $key => $file)
        if (isset ($files[$key]) && $files[$key] && $obj->$key instanceof Uploader)
          if (!$obj->$key->put ($files[$key]))
            return false;
      
      return true;
    });

    $result ? Session::setFlashData ('result.success', '成功！') : Session::setFlashData ('result.failure', '失敗！');
    
    return URL::refresh (RestfulUrl::index ());
  }
  public function destroy ($obj) {
    $result = Article::transaction (function () use ($obj) {
      return $obj->destroy ();
    });

    $result ? Session::setFlashData ('result.success', '成功！') : Session::setFlashData ('result.failure', '失敗！');
    
    return URL::refresh (RestfulUrl::index ());
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
