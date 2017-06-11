<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Articles extends Admin_controller {
  private $uri_1 = null;
  private $obj = null;

  public function __construct () {
    parent::__construct ();
    
    if (!User::current ()->in_roles (array ('article')))
      return redirect_message (array ('admin'), array ('_fd' => '您的權限不足，或者頁面不存在。'));
    
    $this->uri_1 = 'admin/articles';

    if (in_array ($this->uri->rsegments (2, 0), array ('edit', 'update', 'destroy', 'status', 'show')))
      if (!(($id = $this->uri->rsegments (3, 0)) && ($this->obj = Article::find ('one', array ('conditions' => array ('id = ?', $id))))))
        return redirect_message (array ($this->uri_1), array ('_fd' => '找不到該筆資料。'));

    $this->add_param ('uri_1', $this->uri_1)
         ->add_param ('_url', base_url ($this->uri_1));
  }

  public function index ($offset = 0) {
    $searches = array (
        'title'   => array ('el' => 'input', 'text' => '標題', 'sql' => 'title LIKE ?'),
        'status' => array ('el' => 'select', 'text' => '上、下架', 'sql' => 'status = ?', 'items' => array_map (function ($t) {
          return array (
              'text' => Article::$statusNames[$t],
              'value' => $t,
            );
        }, array_keys (Article::$statusNames))),
        'user_id[]' => array ('el' => 'checkbox', 'text' => '使用者', 'sql' => 'user_id IN (?)', 'items' => array_map (function ($u) {
          return array (
              'text' => $u->name,
              'value' => $u->id,
            );
        }, User::all ())),
      );

    $configs = array_merge (explode ('/', $this->uri_1), array ('%s'));
    $objs = conditions ($searches, $configs, $offset, 'Article', array ('order' => 'id DESC', 'include' => array ('user')));

    return $this->load_view (array (
        'objs' => $objs,
        'total' => $offset,
        'searches' => $searches,
        'pagination' => $this->_get_pagination ($configs),
      ));
  }
  public function add () {
    $posts = Session::getData ('posts', true);
    $tag_ids = isset ($posts['tag_ids']) ? $posts['tag_ids'] : array ();
    $sources = isset ($posts['sources']) ? $posts['sources'] : array ();

    $row_muti = array (
        array ('type' => 'text', 'name' => 'sources', 'key' => 'title', 'placeholder' => '標題文字'),
        array ('type' => 'text', 'name' => 'sources', 'key' => 'href', 'placeholder' => '網址'),
      );

    return $this->load_view (array (
        'posts' => $posts,
        'tag_ids' => $tag_ids,
        'sources' => $sources,
        'row_muti' => $row_muti,
      ));
  }
  public function create () {
    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, 'add'), array ('_fd' => '非 POST 方法，錯誤的頁面請求。'));

    $posts = OAInput::post ();
    $posts['content'] = OAInput::post ('content', false);
    $cover = OAInput::file ('cover');
    $images = OAInput::file ('images[]');
    
    if (($msg = $this->_validation_create ($posts, $cover, $images)) || (!Article::transaction (function () use (&$obj, $posts, $cover) {
      if (!verifyCreateOrm ($obj = Article::create (array_intersect_key ($posts, Article::table ()->columns))))
        return false;
      return $obj->cover->put ($cover);
    }) && $msg = '新增失敗！')) return redirect_message (array ($this->uri_1, 'add'), array ('_fd' => $msg, 'posts' => $posts));


    if ($posts['tag_ids'])
      foreach ($posts['tag_ids'] as $tag_id)
        ArticleTagMapping::transaction (function () use ($tag_id, $obj) { return verifyCreateOrm (ArticleTagMapping::create (array_intersect_key (array ('article_tag_id' => $tag_id, 'article_id' => $obj->id), ArticleTagMapping::table ()->columns))); });

    if ($posts['sources'])
      foreach ($posts['sources'] as $i => $source)
        ArticleSource::transaction (function () use ($i, $source, $obj) { return verifyCreateOrm (ArticleSource::create (array_intersect_key (array_merge ($source, array ('sort' => $i, 'article_id' => $obj->id)), ArticleSource::table ()->columns))); });
    
    if ($images)
      foreach ($images as $image)
        ArticleImage::transaction (function () use ($image, $obj) { return verifyCreateOrm ($img = ArticleImage::create (array_intersect_key (array ('article_id' => $obj->id), ArticleImage::table ()->columns))) && $img->name->put ($image); });

    return redirect_message (array ($this->uri_1), array ('_fi' => '新增成功！'));
  }
  public function edit () {
    $posts = Session::getData ('posts', true);
    $tag_ids = isset ($posts['tag_ids']) ? $posts['tag_ids'] : column_array ($this->obj->mappings, 'article_tag_id');
    $sources = isset ($posts['sources']) ? $posts['sources'] : array_map (function ($source) { return array ('title' => $source->title, 'href' => $source->href); }, $this->obj->sources);

    $row_muti = array (
        array ('type' => 'text', 'name' => 'sources', 'key' => 'title', 'placeholder' => '標題文字'),
        array ('type' => 'text', 'name' => 'sources', 'key' => 'href', 'placeholder' => '網址'),
      );

    return $this->load_view (array (
        'posts' => $posts,
        'obj' => $this->obj,
        'tag_ids' => $tag_ids,
        'sources' => $sources,
        'row_muti' => $row_muti,
      ));
  }
  public function update () {
    $obj = $this->obj;

    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_fd' => '非 POST 方法，錯誤的頁面請求。'));

    $posts = OAInput::post ();
    $posts['content'] = OAInput::post ('content', false);
    $cover = OAInput::file ('cover');
    $images = OAInput::file ('images[]');

    if ($msg = $this->_validation_update ($posts, $cover, $images, $obj))
      return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_fd' => $msg, 'posts' => $posts));

    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;

    if (!Article::transaction (function () use ($obj, $posts, $cover) {
      if (!$obj->save ()) return false;
      if ($cover && !$obj->cover->put ($cover)) return false;
      return true;
    })) return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_fd' => '更新失敗！', 'posts' => $posts));

    $ori_ids = column_array ($obj->mappings, 'article_tag_id');

    if (($del_ids = array_diff ($ori_ids, $posts['tag_ids'])) && ($mappings = ArticleTagMapping::find ('all', array ('select' => 'id, article_tag_id', 'conditions' => array ('article_id = ? AND article_tag_id IN (?)', $obj->id, $del_ids)))))
      foreach ($mappings as $mapping)
        ArticleTagMapping::transaction (function () use ($mapping) { return $mapping->destroy (); });

    if (($add_ids = array_diff ($posts['tag_ids'], $ori_ids)) && ($tags = ArticleTag::find ('all', array ('select' => 'id', 'conditions' => array ('id IN (?)', $add_ids)))))
      foreach ($tags as $tag)
        ArticleTagMapping::transaction (function () use ($tag, $obj) { return verifyCreateOrm (ArticleTagMapping::create (Array_intersect_key (array ('article_tag_id' => $tag->id, 'article_id' => $obj->id), ArticleTagMapping::table ()->columns))); });

    if ($obj->sources)
      foreach ($obj->sources as $source)
        ArticleSource::transaction (function () use ($source) { return $source->destroy (); });

    if ($posts['sources'])
      foreach ($posts['sources'] as $i => $source)
        ArticleSource::transaction (function () use ($i, $source, $obj) { return verifyCreateOrm (ArticleSource::create (array_intersect_key (array_merge ($source, array ('sort' => $i, 'article_id' => $obj->id)), ArticleSource::table ()->columns))); });


    if (($del_ids = array_diff (column_array ($obj->images, 'id'), $posts['oldimg'])) && ($imgs = ArticleImage::find ('all', array ('select' => 'id, name', 'conditions' => array ('id IN (?)', $del_ids)))))
      foreach ($imgs as $img)
        ArticleImage::transaction (function () use ($img) { return $img->destroy (); });

    if ($images)
      foreach ($images as $image)
        ArticleImage::transaction (function () use ($image, $obj) { return verifyCreateOrm ($img = ArticleImage::create (array_intersect_key (array ('article_id' => $obj->id), ArticleImage::table ()->columns))) && $img->name->put ($image); });


    return redirect_message (array ($this->uri_1), array ('_fi' => '更新成功！'));
  }

  public function destroy () {
    // $obj = $this->obj;

    // if (!Article::transaction (function () use ($obj) { return $obj->destroy (); }))
    //   return redirect_message (array ($this->uri_1), array ('_fd' => '刪除失敗！'));

    // return redirect_message (array ($this->uri_1), array ('_fi' => '刪除成功！'));
  }

  public function show () {
    return $this->load_view (array (
        'obj' => $this->obj,
      ));
  }
  public function status () {
    $obj = $this->obj;

    if (!$this->has_post ())
      return $this->output_error_json ('非 POST 方法，錯誤的頁面請求。');

    $posts = OAInput::post ();

    $validation = function (&$posts) {
      return !(isset ($posts['status']) && is_string ($posts['status']) && is_numeric ($posts['status'] = trim ($posts['status'])) && ($posts['status'] = $posts['status'] ? Article::STATUS_2 : Article::STATUS_1) && in_array ($posts['status'], array_keys (Article::$statusNames))) ? '「設定上下架」發生錯誤！' : '';
    };

    if ($msg = $validation ($posts))
      return $this->output_error_json ($msg);

    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;

    if (!Article::transaction (function () use ($obj, $posts) {
      return $obj->save ();
    })) return $this->output_error_json ('更新失敗！');

    return $this->output_json ($obj->status == Article::STATUS_2);
  }
  private function _validation_create (&$posts, &$cover, &$images) {
    if (!(isset ($posts['title']) && is_string ($posts['title']) && ($posts['title'] = trim ($posts['title'])))) return '「文章標題」發生錯誤！';
    if (!(isset ($posts['content']) && is_string ($posts['content']) && ($posts['content'] = trim ($posts['content'])))) return '「文章內容」發生錯誤！';
    if (!(isset ($posts['user_id']) && is_string ($posts['user_id']) && is_numeric ($posts['user_id'] = trim ($posts['user_id'])) && User::find_by_id ($posts['user_id']))) return '「文章作者」發生錯誤！';
    if (!(isset ($posts['status']) && is_string ($posts['status']) && is_numeric ($posts['status'] = trim ($posts['status'])) && in_array ($posts['status'], array_keys (Article::$statusNames)))) $posts['status'] = Article::STATUS_1;
    if (isset ($posts['memo']) && !(is_string ($posts['memo']) && ($posts['memo'] = trim ($posts['memo'])))) $posts['memo'] = '';
    
    $posts['tag_ids'] = isset ($posts['tag_ids']) && is_array ($posts['tag_ids']) && $posts['tag_ids'] ? column_array (ArticleTag::find ('all', array ('select' => 'id', 'conditions' => array ('id IN (?)', $posts['tag_ids']))), 'id') : array ();
    $posts['sources'] = isset ($posts['sources']) && is_array ($posts['sources']) && $posts['sources'] ? array_values (array_filter ($posts['sources'], function ($source) { return (isset ($source['title']) && is_string ($source['title']) &&  ($source['title'] = trim ($source['title']))) || (isset ($source['href']) && is_string ($source['href']) &&  ($source['href'] = trim ($source['href']))); })) : array ();

    if (!(isset ($cover) && is_upload_image_format ($cover, array ('gif', 'jpeg', 'jpg', 'png')))) return '「文章封面」發生錯誤！';
    $images = array_values (array_filter ($images, function ($image) { return is_upload_image_format ($image, array ('gif', 'jpeg', 'jpg', 'png')); }));

    return '';
  }
  private function _validation_update (&$posts, &$cover, &$images, $obj) {
    if (isset ($posts['title']) && !(is_string ($posts['title']) && ($posts['title'] = trim ($posts['title'])))) return '「文章標題」發生錯誤！';
    if (isset ($posts['content']) && !(is_string ($posts['content']) && ($posts['content'] = trim ($posts['content'])))) return '「文章內容」發生錯誤！';
    if (isset ($posts['user_id']) && !(is_string ($posts['user_id']) && is_numeric ($posts['user_id'] = trim ($posts['user_id'])) && User::find_by_id ($posts['user_id']))) return '「文章作者」發生錯誤！';
    if (isset ($posts['status']) && !(is_string ($posts['status']) && is_numeric ($posts['status'] = trim ($posts['status'])) && in_array ($posts['status'], array_keys (Article::$statusNames)))) $posts['status'] = Article::STATUS_1;
    if (isset ($posts['memo']) && !(is_string ($posts['memo']) && ($posts['memo'] = trim ($posts['memo'])))) $posts['memo'] = '';

    $posts['tag_ids'] = isset ($posts['tag_ids']) && is_array ($posts['tag_ids']) && $posts['tag_ids'] ? column_array (ArticleTag::find ('all', array ('select' => 'id', 'conditions' => array ('id IN (?)', $posts['tag_ids']))), 'id') : array ();
    $posts['sources'] = isset ($posts['sources']) && is_array ($posts['sources']) && $posts['sources'] ? array_values (array_filter ($posts['sources'], function ($source) { return (isset ($source['title']) && is_string ($source['title']) &&  ($source['title'] = trim ($source['title']))) || (isset ($source['href']) && is_string ($source['href']) &&  ($source['href'] = trim ($source['href']))); })) : array ();

    if (!((string)$obj->cover || isset ($cover))) return '「文章封面」發生錯誤！';
    if (isset ($cover) && !(is_upload_image_format ($cover, array ('gif', 'jpeg', 'jpg', 'png')))) return '「文章封面」發生錯誤！';

    $images = array_values (array_filter ($images, function ($image) { return is_upload_image_format ($image, array ('gif', 'jpeg', 'jpg', 'png')); }));
    $posts['oldimg'] = isset ($posts['oldimg']) ? $posts['oldimg'] : array ();

    return '';
  }
}
