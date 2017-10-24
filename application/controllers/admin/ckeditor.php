<?php if (!defined ('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Ckeditor extends Admin_controller {

  public function image_browser ($type = 'self', $offset = 0) {
    $gets = http_build_query (OAInput::get ());
    $uri_1 = array ('admin', 'ckeditor', 'image_browser');
    $types = array_combine ($u = array_keys ($t = array ('self' => array ('t' => '個人上傳', 'a' => false), 'all' => array ('t' => '全部上傳', 'a' => false))), array_map (function ($t, $u) use ($uri_1, $gets, $type) { $t['a'] = $u === $type;  $t['u'] = base_url (array_merge ($uri_1, array ($u, '?' . $gets))); return $t; }, $t, $u));

    $searches = array ();
    $configs = array_merge ($uri_1, array ($type, '%s', '?' . $gets));
    $objs = conditions ($searches, $configs, $offset, 'CkeditorImage', array ('order' => 'id DESC'), function ($conditions) use ($type) {
      if ($type === 'self') OaModel::addConditions ($conditions, 'user_id = ?', User::current ()->id);
      return $conditions;
    }, 12);

    return $this->set_frame_path ('frame', 'pure')->load_view (array (
        'objs' => $objs,
        'types' => $types,
        'total' => $offset,
        'searches' => $searches,
        'pagination' => $this->_get_pagination ($configs),
      ));
  }
  public function image_upload () {
    $funcNum = $_GET['CKEditorFuncNum'];
    $upload = OAInput::file ('upload');

    if (!($upload && verifyCreateOrm ($img = CkeditorImage::create (array ('name' => '', 'user_id' => User::current ()->id))) && $img->name->put ($upload, true))) echo "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction ($funcNum, '', '上傳失敗！');</script>";
    else echo "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction ($funcNum, '" . $img->name->url ('h800') . "', '上傳成功！');</script>";
  }
  public function dropler_upload () {
    $upload = OAInput::file ('upload');

    if (!($upload && verifyCreateOrm ($img = CkeditorImage::create (array ('name' => '', 'user_id' => User::current ()->id))) && $img->name->put ($upload, true)))
      return $this->output_error_json (array ('上傳失敗！'));
    else
      return $this->output_json (array ('url' => $img->name->url ('h800')));

  }
}
