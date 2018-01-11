<a href="<?php echo RestfulUrl::index ();?>">回列表</a>
<hr>

ID：<?php echo $obj->id;?>
<br/>
封面：
<br/>
<?php echo $obj->cover->toImageTag ('w100');?>
<br/>
標題：<?php echo $obj->title;?>
<br/>
內容：<?php echo $obj->content;?>
<hr/>
最後修改：<?php echo $obj->updated_at;?>
<br/>
產生時間：<?php echo $obj->created_at;?>