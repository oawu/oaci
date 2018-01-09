<a href="<?php echo RestfulUrl::index ();?>">回列表</a>
<hr>
<?php
if ($failure = Session::getFlashData ('result.failure')) { ?>
  <div style='border: 1px solid rgba(233, 77, 68, 1.00);background-color: rgba(233, 77, 68, .300);padding: 8px;'><?php echo $failure;?></div>
  <br>
<?php
}?>

<form action='<?php echo RestfulUrl::update ($tag);?>' method='post'>
  <input type='hidden' name='_method' value='put' />

  * <input type='text' name='name' value='<?php echo $tag->name;?>' autofocus />

  <button type='reset'>重填</button>
  <button type='submit'>送出</button>
</form>