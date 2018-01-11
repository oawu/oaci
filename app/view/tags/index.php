<h1>標籤列表</h1>

<div class='info'>
  <span>總筆數：<?php echo $total;?></span>
  <span><a href="<?php echo RestfulUrl::add ();?>">新增</a></span>
</div>

<div class='msg <?php echo $flash['type'];?>'><?php echo $flash['msg'];?></div>

<table class='table'>
  <thead>
    <tr>
      <th width='50'>ID</th>
      <th>名稱</th>
      <th width='100'>文章數</th>
      <th width='120'>編輯</th>
    </tr>
  </thead>
  <tbody>
<?php
    if (!$objs) { ?>
      <tr>
        <td colspan='4'>沒有資料</td>
      </tr>
<?php
    }
    foreach ($objs as $obj) { ?>
      <tr>
        <td><?php echo $obj->id;?></td>
        <td><?php echo $obj->name;?></td>
        <td>
          <a href="<?php echo RestfulUrl::other ('tag_articles@index', array ($obj));?>"><?php echo count ($obj->articles);?> 篇</a>
        </td>
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
  <span><?php echo implode ('', $pagination);?></span>
</div>
