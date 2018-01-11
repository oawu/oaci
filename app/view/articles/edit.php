<h1>修改文章</h1>

<div class='info'>
  <span><a href="<?php echo RestfulUrl::index ();?>">回列表</a></span>
  <span></span>
</div>

<div class='msg <?php echo $flash['type'];?>'><?php echo $flash['msg'];?></div>

<form class='form' action='<?php echo RestfulUrl::update ($article);?>' method='post' enctype='multipart/form-data'>
  <input type='hidden' name='_method' value='put' />
  
  <label class='required'>
    <b>標題</b>
    <input type='text' name='title' value='<?php echo $params['title'] !== null ? $params['title'] : $article->title;?>' autofocus />
  </label>
  
  <label class='required'>
    <b>分類</b>
    <select name='tag_id'>
      <option value=''>選擇分類</option>
<?php foreach (Tag::all (Where::create ('status = ?', Tag::STATUS_ON)) as $tag) { ?>
        <option value='<?php echo $tag->id;?>'<?php echo ($params['tag_id'] !== null ? $params['tag_id'] : $article->tag_id) == $tag->id ? ' selected' : '';?>><?php echo $tag->name;?></option>
<?php }?>
    </select>
  </label>
  
  <label class='required'>
    <b>封面</b>
    <?php echo $article->cover->toDivImageTag ('w100', array ('class' => 'img'));?>
    <input type='file' name='cover' value='' accept="image/*" />
  </label>
  
  <label>
    <b>內容</b>
    <textarea name='content'><?php echo $params['content'] !== null ? $params['content'] : $article->content;?></textarea>
  </label>

  <div class='btns'>
    <button type='submit'>送出</button>
    <button type='reset'>重填</button>
  </div>
</form>
