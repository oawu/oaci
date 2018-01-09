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
    $validation = function (&$posts, &$files) {
      $error = '';

      $error || isset ($posts['title']) || $error = '參數錯誤！';
      $error || $error = Validation::create ($posts['title'], '標題')->isStringOrNumber ()->length (1, 255)->getError ();

      $error || isset ($posts['tag_id']) || $error = '參數錯誤！';
      $error || $error = Validation::create ($posts['tag_id'], 'Tag')->isNumber ()->greater (0)->getError ();

      $error || isset ($posts['content']) && $error = Validation::create ($posts['content'], '內容')->isStringOrNumber ()->length (0)->getError ();

      $error || isset ($files['cover']) || $error = '參數錯誤！';
      $error || $error = Validation::create ($files['cover'], '封面')->isUploadFile ()->formats ('jpg', 'gif', 'png')->size (1, 10 * 1024 * 1024)->getError ();

      return $error;
    };

    $posts = Input::post ();
    $posts['tag_id'] = $this->parent->id;
    $files = Input::file ();
    
    if ($error = $validation ($posts, $files)) {
      Session::setFlashData ('result.failure', '失敗！' . $error . '！');
      return URL::refresh (RestfulUrl::add ());
    }
    
    if ($error = Article::getTransactionError (function () use ($posts, $files) {
      if (!$obj = Article::create ($posts))
        return false;

      foreach ($files as $key => $file)
        if (isset ($files[$key]) && $files[$key] && $obj->$key instanceof Uploader)
          if (!$obj->$key->put ($files[$key]))
            return false;
      
      return true;
    })) {
      Session::setFlashData ('result.failure', '失敗！' . $error . '！');
      return URL::refresh (RestfulUrl::add ());
    }

    Session::setFlashData ('result.success', '成功！');
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
    $validation = function ($obj, &$posts, &$files) {
      $error = '';

      $error || isset ($posts['title']) && $error = Validation::create ($posts['title'], '標題')->isStringOrNumber ()->length (1, 255)->getError ();
      $error || isset ($posts['tag_id']) && $error = Validation::create ($posts['tag_id'], 'Tag')->isNumber ()->greater (0)->getError ();
      $error || isset ($posts['content']) && $error = Validation::create ($posts['content'], '內容')->isStringOrNumber ()->length (0)->getError ();
      
      $obj->cover->getValue () || $error || isset ($files['cover']) || $error = '參數錯誤！';
      $error || isset ($files['cover']) && $error = Validation::create ($files['cover'], '封面')->isUploadFile ()->formats ('jpg', 'gif', 'png')->size (1, 10 * 1024 * 1024)->getError ();

      return $error;
    };

    $posts = Input::post ();
    $files = Input::file ();

    if ($error = $validation ($obj, $posts, $files)) {
      Session::setFlashData ('result.failure', '失敗！' . $error . '！');
      return URL::refresh (RestfulUrl::edit ($obj));
    }
    
    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;


    if ($error = Article::getTransactionError (function () use ($obj, $files) {
      if (!$obj->save ())
        return false;

      foreach ($files as $key => $file)
        if (isset ($files[$key]) && $files[$key] && $obj->$key instanceof Uploader)
          if (!$obj->$key->put ($files[$key]))
            return false;
      
      return true;
    })) {
      Session::setFlashData ('result.failure', '失敗！' . $error . '！');
      return URL::refresh (RestfulUrl::edit ($obj));
    };

    Session::setFlashData ('result.success', '成功！');
    return URL::refresh (RestfulUrl::index ());
  }
  public function destroy ($obj) {
    if ($error = Article::getTransactionError (function () use ($obj) {
      return $obj->destroy ();
    })) {
      Session::setFlashData ('result.failure', '失敗！' . $error . '！');
      return URL::refresh (RestfulUrl::index ());
    }

    Session::setFlashData ('result.success', '成功！');
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
