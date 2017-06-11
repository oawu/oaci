<h1>新增文章</h1>

<div class='panel'>
  <form class='form-type1' action='<?php echo base_url ($uri_1, $obj->id);?>' method='post' enctype='multipart/form-data'>
    <input type='hidden' name='_method' value='put' />

    <div class='row min'>
      <b class='need'>是否公開</b>
      <label class='switch'>
        <input type='checkbox' name='status'<?php echo (isset ($posts['status']) ? $posts['status'] : $obj->status) == Article::STATUS_2 ? ' checked' : '';?> value='<?php echo Article::STATUS_2;?>' />
        <span></span>
      </label>
    </div>

    <div class='row'>
      <b class='need'>文章作者</b>
      <select name='user_id'>
    <?php if ($users = User::all (array ('select' => 'id, name'))) {
            foreach ($users as $user) { ?>
              <option value='<?php echo $user->id;?>'<?php echo (isset ($posts['user_id']) ? $posts['user_id'] : $obj->user_id) == $user->id ? ' selected': '';?>><?php echo $user->name;?></option>
      <?php }
          }?>
      </select>
    </div>
      
<?php if ($tags = ArticleTag::all ()) { ?>
        <div class='row'>
          <b class='need'>文章分類</b>
    <?php foreach ($tags as $tag) { ?>
            <label class='checkbox'>
              <input type='checkbox' name='tag_ids[]' value='<?php echo $tag->id;?>'<?php echo $tag_ids && in_array ($tag->id, $tag_ids) ? ' checked' : '';?>>
              <span></span>
              <?php echo $tag->name;?>
            </label>
    <?php } ?>
        </div>
<?php }?>

    <div class='row'>
      <b class='need' data-title='預覽僅示意，未按比例。'>文章封面</b>
      <div class='drop_img'>
        <img src='<?php echo $obj->cover->url ();?>' />
        <input type='file' name='cover' />
      </div>
    </div>

    <div class='row'>
      <b>其他照片</b>
      <div class='drop_imgs'>
  <?php foreach ($obj->images as $image) { ?>
          <div class='drop_img'>
            <img src='<?php echo $image->name->url ();?>' />
            <input type='hidden' name='oldimg[]' value='<?php echo $image->id; ?>' />
            <input type='file' name='images[]' />
            <a class='icon-bin'></a>
          </div>
  <?php }?>

        <div class='drop_img'>
          <img src='' />
          <input type='file' name='images[]' />
          <a class='icon-bin'></a>
        </div>

      </div>

    </div>

    <div class='row'>
      <b class='need'>文章標題</b>
      <input type='text' name='title' value='<?php echo isset ($posts['title']) ? $posts['title'] : $obj->title;?>' placeholder='請輸入文章標題..' maxlength='200' pattern='.{1,200}' required title='輸入文章標題!' autofocus />
    </div>
    
    <div class='row'>
      <b class='need' data-title='請補足10個字'>文章內容</b>
      <textarea name='content' class='cke' placeholder='請輸入文章內容..'><?php echo isset ($posts['content']) ? $posts['content'] : $obj->content;?></textarea>
    </div>

    <div class='row'>
      <b>文章備註</b>
      <textarea name='memo' placeholder='請輸入文章備註..'><?php echo isset ($posts['memo']) ? $posts['memo'] : $obj->memo;?></textarea>
    </div> 

    <div class='row muti' data-vals='<?php echo json_encode ($sources);?>' data-cnt='<?php echo count ($row_muti);?>' data-attrs='<?php echo json_encode ($row_muti);?>'>
      <b>文章參考</b>
      <span><a></a></span>
    </div>


    <div class='row'>
      <button type='submit'>確定送出</button>
      <button type='reset'>重新填寫</button>
      <a href='<?php echo base_url ($uri_1);?>'>回上一頁</a>
    </div>
  </form>
</div>

