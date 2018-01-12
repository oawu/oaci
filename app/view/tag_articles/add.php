<h1>新增文章</h1>

<div class='info'>
  <span><a href="<?php echo RestfulUrl::index ();?>">回列表</a></span>
  <span></span>
</div>

<div class='msg <?php echo $flash['type'];?>'><?php echo $flash['msg'];?></div>

<form class='form' action='<?php echo RestfulUrl::create ();?>' method='post' enctype='multipart/form-data'>
  <label class='required'>
    <b>標題</b>
    <input type='text' name='title' value='<?php echo $params['title'];?>' autofocus />
  </label>
  
  <label class='required'>
    <b>封面</b>
    <input type='file' name='cover' value='' accept="image/*" />
  </label>
  
  <label>
    <b>內容</b>
    <textarea name='content'><?php echo $params['content'];?></textarea>
  </label>

  <div class='btns'>
    <button type='submit'>送出</button>
    <button type='reset'>重填</button>
  </div>
</form>
