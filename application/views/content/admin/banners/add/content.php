<h1<?php echo isset ($icon) && $icon ? ' class="' . $icon . '"' : '';?>>新增<?php echo $title;?></h1>

<div class='panel'>
  <form class='form-type1 loading' action='<?php echo base_url ($uri_1);?>' method='post' enctype='multipart/form-data'>

    <div class='row min'>
      <b class='need'>是否上架</b>
      <label class='switch'>
        <input type='checkbox' name='status'<?php echo (isset ($posts['status']) ? $posts['status'] : Banner::STATUS_1) == Banner::STATUS_2 ? ' checked' : '';?> value='<?php echo Banner::STATUS_2;?>' />
        <span></span>
      </label>
    </div>

    <div class='row'>
      <b class='need' data-title='預覽僅示意，未按比例。'><?php echo $title;?>封面</b>
      <div class='drop_img'>
        <img src='' />
        <input type='file' name='cover' />
      </div>
    </div>


    <div class='row'>
      <b class='need'><?php echo $title;?>標題</b>
      <input type='text' name='title' value='<?php echo isset ($posts['title']) ? $posts['title'] : '';?>' placeholder='請輸入<?php echo $title;?>標題..' maxlength='200' pattern='.{1,200}' required title='輸入<?php echo $title;?>標題!' autofocus />
    </div>
    
    <div class='row'>
      <b class='need'><?php echo $title;?>內容</b>
      <textarea class='pure' name='content' placeholder='請輸入<?php echo $title;?>內容..'><?php echo isset ($posts['content']) ? $posts['content'] : '';?></textarea>
    </div>


    <div class='row'>
      <b class='need'><?php echo $title;?>鏈結</b>
      <input type='text' name='link' value='<?php echo isset ($posts['link']) ? $posts['link'] : '';?>' placeholder='請輸入<?php echo $title;?>鏈結..' maxlength='200' pattern='.{1,200}' required title='輸入<?php echo $title;?>鏈結!' />
    </div>

    <div class='row'>
      <b class='need'>鏈結開啟方式</b>
<?php foreach (Banner::$targetNames as $key => $targetName) { ?>
        <label class='radio'>
          <input type='radio' name='target' value='<?php echo $key;?>'<?php echo (isset ($posts['target']) ? $posts['target'] : Banner::TARGET_1) == $key ? ' checked' : '';?>>
          <span></span><?php echo $targetName;?>
        </label>
<?php } ?>
    </div>


    <div class='row'>
      <button type='submit'>確定送出</button>
      <button type='reset'>重新填寫</button>
      <a href='<?php echo base_url ($uri_1);?>'>回列表頁</a>
    </div>
  </form>
</div>
