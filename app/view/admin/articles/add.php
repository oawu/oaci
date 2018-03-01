<div class='back'>
  <a href="<?php echo RestfulUrl::index ();?>" class='icon-36'>回上一頁</a>
</div>

<?php echo $form->appendFormRows (
  // Restful\Text::need ('標題', 'title', 'ssss')->setAutofocus (true)->setMaxLength (255)
  // Restful\Ckeditor::need ('內容', 'content')
  // Restful\Image::need ('封面', 'cover')->setTip ('預覽僅示意，未按比例')->setAccept ('image/*')
  // Restful\Images::need ('其他照片', 'images')->setTip ('可上傳多張，預覽僅示意，未按比例')->setAccept ('image/*')
  // Restful\Selecter::need ('作者', 'user_id', '2')->setItemObjs (User::find ('all', array ('select' => 'id, name')), 'id', 'name')
  // Restful\Radior::need ('開啟方式', 'action')->setItemKVs (Article::$actionTexts)
  // Restful\Switcher::need ('是否啟用', 'status')->setCheckedValue (Article::STATUS_ON)
  // Restful\Checkboxs::need ('標籤', 'tag_ids')->setItemObjs (Tag::find ('all', array ('select' => 'id, name')), 'id', 'name')
  Restful\LatLng::need ('座標', 'lat', 'lng')
);?>