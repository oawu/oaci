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
    $this->load->library ('Scws');

    $data = Scws::explode ('涼風有訊 秋月無邊 虧我思嬌的情緒好比度日如年 雖然我不是玉樹臨風 瀟灑倜儻 但我有廣闊的胸襟加強健的臂彎');
echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
echo '<br/>涼風有訊 秋月無邊 虧我思嬌的情緒好比度日如年<br/>雖然我不是玉樹臨風 瀟灑倜儻 但我有廣闊的胸襟加強健的臂彎<br/>';
echo '<br/>= tags =========================<br/><br/>';
foreach ($data as $key => $value) {
  echo $value . '<br/>';
}
exit ();
    $this->load_view (null);
  }
}
