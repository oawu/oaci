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
    // $e = Event::create (array ('title' => 'x', 'info' => 'x', 'cover' => 'x'));

    // $e = Event::find (1);
    // echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
    // var_dump ($e->cover->url ());
    // exit ();;
    echo "<form action='" . base_url ($this->get_class (), 'x') . "' method='post' enctype='multipart/form-data'>";
    echo "<input type='file' name='file' />";
    echo "<button type='submit'>submit</button>";
    echo "</form>";
  }
  public function x () {
    $file = $this->input_post ('file', true, true);;
    $e = Event::create (array ('title' => 'x', 'info' => 'x', 'cover' => 'x'));
    echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
    var_dump ($e->cover->put ($file));
    exit ();
  }
  public function o () {
    $e = Event::find (45);
    echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
    var_dump ($e->cover->cleanAllFiles ());
    exit ();
  }
}
