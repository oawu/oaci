<h1<?php echo isset ($icon) && $icon ? ' class="' . $icon . '"' : '';?>>修改<?php echo $title;?></h1>

<div class='panel'>
  <form class='form-type1 loading' action='<?php echo base_url ($uri_1, $obj->id);?>' method='post'>
    <input type='hidden' name='_method' value='put' />

    <div class='row'>
      <b class='need'><?php echo $title;?>名稱</b>
      <input type='text' name='name' value='<?php echo isset ($posts['name']) ? $posts['name'] : $obj->name;?>' placeholder='請輸入<?php echo $title;?>名稱..' maxlength='200' pattern='.{1,200}' required title='輸入<?php echo $title;?>名稱!' autofocus />
    </div>
    
    <div class='row'>
      <button type='submit'>確定送出</button>
      <button type='reset'>重新填寫</button>
      <a href='<?php echo base_url ($uri_1);?>'>回列表頁</a>
    </div>
  </form>
</div>
