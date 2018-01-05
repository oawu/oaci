<a href="<?php echo RestfulUrl::index ();?>">回列表</a>
<hr>

<form action='<?php echo RestfulUrl::update ($article);?>' method='post' enctype='multipart/form-data'>
  <input type='hidden' name='_method' value='put' />

  <input type='text' name='title' value='<?php echo $article->title;?>' />
  <br/>

  <br/>
  <input type='file' name='cover' value='' accept="image/*"/>
  <br/>

  <br/>
  <textarea name='content'><?php echo $article->content;?></textarea>
  <br/>

  <button type='reset'>重填</button>
  <button type='submit'>送出</button>
</form>