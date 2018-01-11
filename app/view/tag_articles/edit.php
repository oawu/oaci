<a href="<?php echo RestfulUrl::index ();?>">回列表</a>
<hr>
<?php
if ($failure = Session::getFlashData ('result.failure')) { ?>
  <div style='border: 1px solid rgba(233, 77, 68, 1.00);background-color: rgba(233, 77, 68, .300);padding: 8px;'><?php echo $failure;?></div>
  <br>
<?php
}?>

<form action='<?php echo RestfulUrl::update ($obj);?>' method='post' enctype='multipart/form-data'>
  <input type='hidden' name='_method' value='put' />

  * <input type='text' name='title' value='<?php echo $obj->title;?>' autofocus />
  <br/>

  <br/>
  * <input type='file' name='cover' value='' accept="image/*"/>
  <br/>

  <br/>
  <textarea name='content'><?php echo $obj->content;?></textarea>
  <br/>

  <button type='reset'>重填</button>
  <button type='submit'>送出</button>
</form>