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
  public function a () {
    $e = Event::create (array ('title' => 'x', 'info' => 'x', 'cover' => 'x'));
    var_dump ($e->cover->put_url ('http://oaci.ioa.tw/upload/events/cover/0/0/0/1/_1162840665_54fea56e143d8.jpg'));
    exit ();
  }
  public function o () {
    $e = Event::find (76);
    echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
    var_dump ($e->cover->save_as ('100ws', array ('adaptiveResizeQuadrant', 130, 130, 't')));
    exit ();
  }
  public function b () {
    $this->load->library ('image/ImageUtility');
    $file = FCPATH . 'temp/demo.png';
    $save = FCPATH . 'temp/x.png';
    $font = FCPATH . 'resource/font/monaco/monaco.ttf';

    try {
        $img = ImageUtility::create ($file);
        $img->addFont ('xxxx', $font, '100', '120', '#fff', '12', '1', '580');
        // exit ();;
        $img->save ($save);

    } catch (Exception $e) {
        error ($e->getMessages());
    }

    // $a = new ImageUtility ();

  }
}
