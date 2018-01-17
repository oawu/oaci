<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class main extends AdminController {

  public function __construct () {
    parent::__construct ();
  }

  public function status () {
    return Output::json (array (
        'status' => 'off'
      ));
  }
  public function index () {
    $this->layout
         ->with ('current_url', URL::base ('admin'));

    $this->asset->addCSS ('/assets/css/admin/list.css')
                ->addJS ('/assets/js/admin/list.js');

    return $this->view->setPath ('admin/list.php')
                ->output ();
  }
  public function index2 () {
    $this->layout
         ->with ('current_url', URL::base ('admin'));

    $this->asset->addCSS ('/assets/css/admin/list.css')
                ->addJS ('/assets/js/admin/list.js');

    return $this->view->setPath ('admin/list2.php')
                ->output ();
  }
  public function form () {
    $this->layout
         ->with ('current_url', URL::base ('admin'));

    $this->asset->addCSS ('/assets/css/admin/form.css')
                ->addJS ('/assets/js/admin/form.js');

    return $this->view->setPath ('admin/form.php')
                ->output ();
  }
  public function show () {
    $this->layout
         ->with ('current_url', URL::base ('admin'));

    $this->asset->addCSS ('/assets/css/admin/show.css')
                ->addJS ('/assets/js/admin/show.js');

    return $this->view->setPath ('admin/show.php')
                ->output ();
  }
}
