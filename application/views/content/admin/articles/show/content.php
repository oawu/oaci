<h1>新增文章</h1>

<div class='panel back'>
  <a class='icon-keyboard_arrow_left' href='<?php echo base_url ($uri_1);?>'>回列表</a>

  <a class='icon-pencil2' href='<?php echo base_url ($uri_1, $obj->id, 'edit');?>'>編輯</a>
  <a class='icon-bin' href='<?php echo base_url ($uri_1, $obj->id);?>' data-method='delete'>刪除</a>
</div>

<div class='panel'>
  <div class='show-type1'>

    <div class='row min'>
      <b>是否公開</b>
      <span><?php echo $obj->status;?></span>
    </div>

    <div class='row min'>
      <b>文章作者</b>
      <span><?php echo $obj->user->name;?></span>
    </div>
      
      <div class='row'>
        <b>文章分類</b>
        <span class='tags<?php echo !$obj->tags ? ' e' : '';?>'>
    <?php foreach ($obj->tags as $tag) { ?>
            <a class='tag'><?php echo $tag->name;?></a>
    <?php } ?>
        </span>
      </div>

    <div class='row'>
      <b data-title='預覽僅示意，未按比例。'>文章封面</b>
      <div class='img'><img src='<?php echo $obj->cover->url ();?>' /></div>
    </div>

    <div class='row'>
      <b>其他照片</b>
      <div class='imgs<?php echo !$obj->images ? ' e' : '';?>'>
  <?php foreach ($obj->images as $image) { ?>
          <div class='img'><img src='<?php echo $image->name->url ();?>' /></div>
  <?php }?>
      </div>
    </div>

    <div class='row'>
      <b>文章標題</b>
      <span><?php echo $obj->title;?></span>
    </div>
    
    <div class='row'>
      <b>文章內容</b>
      <span><?php echo $obj->content;?></span>
    </div>

    <div class='row'>
      <b>文章備註</b>
      <span><?php echo $obj->memo;?></span>
    </div> 

    <div class='row muti' data-vals='<?php echo json_encode ($sources);?>' data-cnt='<?php echo count ($row_muti);?>' data-attrs='<?php echo json_encode ($row_muti);?>'>
      <b>文章參考</b>
      <span class='list<?php echo !$obj->sources ? ' e' : '';?>' data-cnt='2'>
    <?php foreach ($obj->sources as $source) { ?>
        <div><span><?php echo $source->title;?></span><a href='<?php echo $source->href;?>' target='_blank'><?php echo $source->href;?></a></div>
    <?php } ?>

      </span>
    </div>


  </div>
</div>

