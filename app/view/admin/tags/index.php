<div>總筆數：<?php echo $total;?></div>
<a href="<?php echo RestfulUrl::add ();?>">新增</a>

<hr>

<?php
if ($success = Session::getFlashData ('result.success')) { ?>
  <div style='border: 1px solid rgba(86, 145, 242, 1.00);background-color: rgba(86, 145, 242, .300);padding: 8px;'><?php echo $success;?></div>
  <br>
<?php
}?>
<?php
if ($failure = Session::getFlashData ('result.failure')) { ?>
  <div style='border: 1px solid rgba(233, 77, 68, 1.00);background-color: rgba(233, 77, 68, .300);padding: 8px;'><?php echo $failure;?></div>
  <br>
<?php
}?>

<table border='1'>
  <thead>
    <tr>
      <th>ID</th>
      <th>名稱</th>
      <th>文章</th>
      <th>編輯</th>
    </tr>
  </thead>
  <tbody>
<?php
    if (!$tags) { ?>
      <tr>
        <td colspan='4'>沒有資料</td>
      </tr>
<?php
    }
    foreach ($tags as $tag) { ?>
      <tr>
        <td><?php echo $tag->id;?></td>
        <td><?php echo $tag->name;?></td>
        <td>
          <a href="<?php echo RestfulUrl::other ('admin/tag_articles@index', array ($tag));?>">檢視(<?php echo count ($tag->articles);?>)</a>
        </td>
        <td>
          <a href="<?php echo RestfulUrl::show ($tag);?>">檢視</a>
          <a href="<?php echo RestfulUrl::edit ($tag);?>">修改</a>
          <a href="<?php echo RestfulUrl::destroy ($tag);?>" data-method='delete'>刪除</a>
        </td>
      </tr>
<?php
    }?>
  </tbody>
</table>

<?php echo implode (' ', $pgn);?>