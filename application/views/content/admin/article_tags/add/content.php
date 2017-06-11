<h1>新增分類</h1>

<div class='panel'>
  <form class='form-type1' action='<?php echo base_url ($uri_1);?>' method='post'>

    <div class='row'>
      <b class='need' data-title='文章分類的名稱'>分類名稱</b>
      <input type='text' name='name' value='<?php echo isset ($posts['name']) ? $posts['name'] : '';?>' placeholder='請輸入分類名稱..' maxlength='200' pattern='.{1,200}' required title='輸入分類名稱!' autofocus />
    </div>

    <div class='row'>
      <button type='submit'>確定送出</button>
      <button type='reset'>重新填寫</button>
      <a href='<?php echo base_url ($uri_1);?>'>回上一頁</a>
    </div>
  </form>
</div>
