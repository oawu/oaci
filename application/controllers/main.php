<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2015 OA Wu Design
 */

class Main extends Site_controller {

  public function __construct () {
    parent::__construct ();
  }

  public function index () {
    echo "<form action='" . base_url ($this->get_class (), 'a') . "' method='post' enctype='multipart/form-data'>";
      echo "<input type='file' name='file' />";
      echo "<button type='submit'>submit</button>";
    echo "</form>";
  }
  public function a () {
    $file = $this->input_post ('file', true);
    // $e = Event::create (array ('title' => '', 'cover' => 'x', 'info' => 'x'));
    $e = Event::find ('one', array ('order' => 'id DESC', 'conditions' => array ('')));
    echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
    var_dump ($e->cover->put ($file));
    exit ();
  }
  public function b () {
    $e = Event::find ('one', array ('order' => 'id DESC', 'conditions' => array ('')));

    echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
    var_dump ($e->cover->url ());
    exit ();
  }
  public function c () {
    // $file = $this->input_post ('file', true);
    // $e = Event::create (array ('title' => '', 'cover' => 'x', 'info' => 'x'));
    $e = Event::find ('one', array ('order' => 'id DESC', 'conditions' => array ('')));
    echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
    var_dump ($e->cover->put_url ('http://oaci.ioa.tw/temp/demo1.jpg'));
    exit ();
  }

}
