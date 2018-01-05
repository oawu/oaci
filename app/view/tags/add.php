<a href="<?php echo RestfulUrl::index ();?>">回列表</a>
<hr>

<form action='<?php echo RestfulUrl::create ();?>' method='post'>
  <input type='text' name='name' value='' />

  <button type='reset'>重填</button>
  <button type='submit'>送出</button>
</form>