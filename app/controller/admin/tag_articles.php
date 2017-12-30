<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2017, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class tag_articles extends RestfulController {
  // public $self_model = 'Tag';
  // public $parent_models = array ('Tag', 'Article');
  // public function __construct () {
  //   parent::__construct ();
  //   // Restful::parents ($this)
  // }

  // public function setParents () {
  //   return array (); 
  // }
  public function index () {


echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
var_dump (Restful::index ());
var_dump (Restful::add ());
var_dump (Restful::create ());
var_dump (Restful::show (1));
var_dump (Restful::edit (Tag::first ()));
var_dump (Restful::update (2));
var_dump (Restful::destroy (3));

var_dump (Restful::custom (Tag::first (), 'weqwe'));
var_dump ($this->parents);
  
  }
  // add
  public function add () {
    
  }
  // create
  public function create () {
  }
  public function edit ($obj) {
    echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
    var_dump (Restful::index ());
    var_dump (Restful::add ());
    var_dump (Restful::create ());
    var_dump (Restful::show (1));
    var_dump (Restful::edit (Tag::first ()));
    var_dump (Restful::update (2));
    var_dump (Restful::destroy (3));

    // echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
    // var_dump ($obj);
    // exit ();
  }
  public function update ($obj) {
  }
  public function destroy ($obj) {
  }
  public function show ($obj) {
  }
}
