<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class main extends Controller {
  public function index () {

    $tags = Tag::find ('all', array (
      // 'include' => array ('articles'),
      'where' => array ('id IN (?)', array (1, 2, 3, 4, 5))));

    $tag_ids = array_map (function ($t) { return $t->id; }, $tags);
    $articles = Article::find ('all', array ('where' => array ('tag_id IN (?)', $tag_ids)));

    $tmp = array ();
    foreach ($articles as $article) {
      if (isset ($tmp[$article->tag_id]))
        array_push ($tmp[$article->tag_id], $article);
      else
        $tmp[$article->tag_id] = array ($article);
    }

    echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
    foreach ($tags as $tag) {
      var_dump ($tag->name);

      foreach ($tmp[$tag->id] as $article) {
        echo $article->title;
        echo "<br/>";
      }
    
      echo "<hr/>";
    }
    

    // echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
    // foreach ($tags as $tag) {
    //   var_dump ($tag->name);

    //   foreach ($tag->articles as $article) {
    //     echo $article->title;
    //     echo "<br/>";
    //   }
    
    //   echo "<hr/>";
    // }


    // $tag = Tag::find_by_id (1);


    

    // $tag = Tag::find_by_id (2);
    // var_dump ($tag->name);

    // foreach ($tag->articles as $article) {
    //   echo $article->title;
    //   echo "<br/>";
    // }
    





    // $articles = Article::find ('all', array ('where' => array ('tag_id = ?', $tag->id)));
    // foreach ($articles as $article) {
    //   echo $article->title;
    //   echo "<br/>";
    // }
  }
}
