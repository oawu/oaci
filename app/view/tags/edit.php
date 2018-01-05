<a href="<?php echo RestfulUrl::index ();?>">回列表</a>
<hr>

<form action='<?php echo RestfulUrl::update ($tag);?>' method='post'>
  <input type='hidden' name='_method' value='put' />

  <input type='text' name='name' value='<?php echo $tag->name;?>' />

  <button type='reset'>重填</button>
  <button type='submit'>送出</button>
</form>