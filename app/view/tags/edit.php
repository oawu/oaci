<h1>修改標籤</h1>

<div class='info'>
  <span><a href="<?php echo RestfulUrl::index ();?>">回列表</a></span>
  <span></span>
</div>

<div class='msg <?php echo $flash['type'];?>'><?php echo $flash['msg'];?></div>

<form class='form' action='<?php echo RestfulUrl::update ($obj);?>' method='post'>
  <input type='hidden' name='_method' value='put' />

  <label class='required'>
    <b>名稱</b>
    <input type='text' name='name' value='<?php echo $params['name'] !== null ? $params['name'] : $obj->name;?>' autofocus />
  </label>

  <div class='btns'>
    <button type='submit'>送出</button>
    <button type='reset'>重填</button>
  </div>
  
</form>
