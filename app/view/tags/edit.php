<a href="<?php echo Restful::index ();?>">回列表</a>
<hr>

<form action='<?php echo Restful::update ($tag);?>' method='post'>
  <input type='hidden' name='_method' value='put' />

  <input type='text' name='name' value='<?php echo $tag->name;?>' />
  <button type='submit'>送出</button>
  <button type='reset'>重填</button>
</form>