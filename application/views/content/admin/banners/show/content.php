<h1<?php echo isset ($icon) && $icon ? ' class="' . $icon . '"' : '';?>>檢視<?php echo $title;?></h1>

<div class='panel back'>
  <a class='icon-keyboard_arrow_left' href='<?php echo base_url ($uri_1);?>'>回列表頁</a>

  <a class='icon-bin' href='<?php echo base_url ($uri_1, $obj->id);?>' data-method='delete'>刪除</a>
  <a class='icon-pencil2' href='<?php echo base_url ($uri_1, $obj->id, 'edit');?>'>編輯</a>
</div>

<div class='panel'>
  <div class='show-type1'>

    <div class='row min'>
      <b>是否上架</b>
      <span><?php echo Banner::$statusNames[$obj->status];?></span>
    </div>

    <div class='row'>
      <b data-title='預覽僅示意，未按比例。'><?php echo $title;?>封面</b>
      <div class='img'><img src='<?php echo $obj->cover->url ();?>' /></div>
    </div>

    <div class='row'>
      <b><?php echo $title;?>標題</b>
      <span><?php echo $obj->title;?></span>
    </div>
    
    <div class='row'>
      <b><?php echo $title;?>內容</b>
      <span><?php echo $obj->content;?></span>
    </div>

    <div class='row'>
      <b><?php echo $title;?>鏈結</b>
      <span><?php echo mini_link ($obj->link, 50);?></span>
    </div> 

    <div class='row min'>
      <b>鏈結開啟方式</b>
      <span><?php echo Banner::$targetNames[$obj->target];?></span>
    </div> 



  </div>
</div>

