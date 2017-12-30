<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class tag_articles extends RestfulController {
  public $self_model = 'Tag';
  public $parent_models = array ('Tag', 'Article');
  // public function __construct () {
  //   parent::__construct ();
  // }
  // list
  public function index () {
    
print (json_encode(Router::$routers));
exit ();
    $total = Article::count ();
    $articles = Article::find ('all');


echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
var_dump (Restful::add ());
var_dump (Restful::create ());
var_dump (Restful::show (1));
var_dump (Restful::edit (2));
var_dump (Restful::edit (2));
var_dump (Restful::destroy (3));
var_dump ($this->parents);
    // return View::create ('articles/index.php')
    //            ->with ('total', $total)
    //            ->with ('articles', $articles)
    //            ->output ();
  }
  // add
  public function add () {
  }
  // create
  public function create () {
  }
  public function edit ($id) {
    echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
    var_dump (Restful::index ());
    var_dump (Restful::add ());
    var_dump (Restful::create ());
    var_dump (Restful::show (1));
    var_dump (Restful::edit (Tag::first ()));
    var_dump (Restful::update (2));
    var_dump (Restful::destroy (3));

  }
  public function update ($id) {
  }
  public function destroy ($id) {
  }
  public function show ($id) {
  }
}
