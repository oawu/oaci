

<!-- <div>Total:<?php echo $total;?></div>
<a href="<?php echo RestfulUrl::add ();?>">新增</a>
<hr>
<table border='1'>
  <thead>
    <tr>
      <th>ID</th>
      <th>標題</th>
      <th>作者</th>
      <th>編輯</th>
    </tr>
  </thead>
  <tbody>
<?php foreach ($articles as $article) { ?>
        <tr>
          <td><?php echo $article->id;?></td>
          <td><?php echo $article->title;?></td>
          <td><?php echo $article->user->name;?></td>
          <td>
            <a href="<?php echo RestfulUrl::show ($article);?>">檢視</a>
            <a href="<?php echo RestfulUrl::edit ($article);?>">修改</a>
            <a href="<?php echo RestfulUrl::destroy ($article);?>">刪除</a>
          </td>
        </tr>
<?php }?>
  </tbody>
</table> -->