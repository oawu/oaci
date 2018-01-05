<a href="<?php echo RestfulUrl::index ();?>">回列表</a>
<hr>

<form action='<?php echo RestfulUrl::create ();?>' method='post' enctype='multipart/form-data'>
  <input type='text' name='title' value='' />
  <br/>
  
  <br/>
  <input type='file' name='cover' accept="image/*"/>
  <br/>

  <br/>
  <textarea name='content'></textarea>
  <br/>

  <button type='reset'>重填</button>
  <button type='submit'>送出</button>
</form>