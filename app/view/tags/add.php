<a href="<?php echo Restful::index ();?>">回列表</a>
<hr>

<form action='<?php echo Restful::create ();?>' method='post'>
  <input type='text' name='name' value='' />
  <button type='submit'>送出</button>
  <button type='reset'>重填</button>
</form>