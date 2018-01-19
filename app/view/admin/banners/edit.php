<div class='back'>
  <a href="<?php echo RestfulUrl::index ();?>" class='icon-36'>回上一頁</a>
</div>

<form class='form' action='<?php echo RestfulUrl::update ($obj);?>' method='post' enctype='multipart/form-data'>
  <input type='hidden' name='_method' value='put' />

  <div class='row min'>
    <b class='need'>是否啟用</b>
    <div class='switches'>
<?php echo form_switch ('status', Banner::STATUS_ON, get_flash_params ('status', $obj->status, Banner::STATUS_ON));?>
    </div>
  </div>

  <label class='row'>
    <b class='need'>標題</b>
    <input type='text' name='title' placeholder='請填寫標題' value='<?php echo get_flash_params ('title', $obj->title);?>' autofocus maxlength='255' pattern='.{1,255}' required/>
  </label>

  <div class='row'>
    <b class='need' data-tip='預覽僅示意，未按比例'>封面</b>
    <div class='drop-img'>
      <?php echo $obj->cover->toImageTag ();?>
      <input type='file' name='cover' accept="image/*" />
    </div>
  </div>

  <div class='row'>
    <b>內容</b>
    <textarea class='ckeditor' name='content'><?php echo get_flash_params ('content', $obj->content);?></textarea>
  </div>

  <label class='row'>
    <b>鏈結</b>
    <input type='text' name='link' placeholder='請填寫鏈結' value='<?php echo get_flash_params ('link', $obj->link);?>' />
  </label>

  <div class='row'>
    <b class='need' data-tip='此設定要有設定鏈結才有用'>鏈結開啟方式</b>
    <div class='radios'>
<?php foreach (Banner::$linkActionTexts as $key => $value) {
        echo form_radio ('link_action', $key, $value, get_flash_params ('link_action', $obj->link_action, $key));?>
<?php } ?>
    </div>
  </div>

  <div class='ctrl'>
    <button type='submit'>確定</button>
    <button type='reset'>取消</button>
  </div>
</form>