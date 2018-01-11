<a href="<?php echo RestfulUrl::url ('admin/tags@index');?>">回 Tags</a>
<hr>

<div><?php echo $parent->name;?> 下的文章，總筆數：<?php echo $total;?></div>
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
      <th>封面</th>
      <th>標題</th>
      <th>內容</th>
      <th>編輯</th>
    </tr>
  </thead>
  <tbody>
<?php
    if (!$articles) { ?>
      <tr>
        <td colspan='5'>沒有資料</td>
      </tr>
<?php
    }
    foreach ($articles as $article) { ?>
      <tr>
        <td><?php echo $article->id;?></td>
        <td><?php echo $article->cover->toImageTag ('w100');?></td>
        <td><?php echo $article->title;?></td>
        <td><?php echo $article->content;?></td>
        <td>
          <a href="<?php echo RestfulUrl::show ($article);?>">檢視</a>
          <a href="<?php echo RestfulUrl::edit ($article);?>">修改</a>
          <a href="<?php echo RestfulUrl::destroy ($article);?>" data-method='delete'>刪除</a>
        </td>
      </tr>
<?php
    }?>
  </tbody>
</table>

<?php echo implode (' ', $pgn);?>