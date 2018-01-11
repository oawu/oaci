<h1>文章列表</h1>

<div class='info'>
  <span>總筆數：<?php echo $total;?></span>
  <span><a href="<?php echo RestfulUrl::add ();?>">新增</a></span>
</div>

<div class='msg <?php echo $flash['type'];?>'><?php echo $flash['msg'];?></div>

<table class='table'>
  <thead>
    <tr>
      <th width='50'>ID</th>
      <th width='120'>封面</th>
      <th>標題</th>
      <th width='120'>分類</th>
      <th width='120'>編輯</th>
    </tr>
  </thead>
  <tbody>
<?php
    if (!$objs) { ?>
      <tr>
        <td colspan='5'>沒有資料</td>
      </tr>
<?php
    }
    foreach ($objs as $obj) { ?>
      <tr>
        <td><?php echo $obj->id;?></td>
        <td><?php echo $obj->cover->toDivImageTag ('w100', array ('class' => 'img'));?></td>
        <td><?php echo $obj->title;?></td>
        <td><?php echo $obj->tag ? $obj->tag->name : '';?></td>
        <td>
          <a href="<?php echo RestfulUrl::show ($obj);?>">檢視</a>
          <a href="<?php echo RestfulUrl::edit ($obj);?>">修改</a>
          <a href="<?php echo RestfulUrl::destroy ($obj);?>" data-method='delete'>刪除</a>
        </td>
      </tr>
<?php
    }?>
  </tbody>
</table>

<div class='pagination'>
  <span><?php echo implode ('', $pgn);?></span>
</div>
