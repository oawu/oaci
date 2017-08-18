<h1<?php echo isset ($icon) && $icon ? ' class="' . $icon . '"' : '';?>><?php echo $title;?>排序</h1>

<div class='panel back'>
  <a class='icon-keyboard_arrow_left' href='<?php echo base_url ($uri_1);?>'>回列表頁</a>
</div>

<form data-desc='請拖曳排序位置。' class='form-type2 loading' action='' method='post'>
  <input type='hidden' name='_method' value='put' />

  <div class='sorts'>
  <?php
    foreach ($objs as $i => $obj) { ?>
      <div class='sort' data-i='<?php echo $i + 1;?>'>
        <div class='_ic'><img src='<?php echo $obj->cover->url ('800w');?>'></div>
        <span title='<?php echo $obj->title;?>'><?php echo $obj->title;?></span>
        <input type='hidden' name='ids[]' value='<?php echo $obj->id;?>' />
      </div>
  <?php
    } ?>
  </div>

  <div class='row'>
    <button type='submit'>確定送出</button>
    <a href=''>重新填寫</a>
    <a href='<?php echo base_url ($uri_1);?>'>回列表頁</a>
  </div>
</form>

<div class='pagination'><?php echo $pagination;?></div>
