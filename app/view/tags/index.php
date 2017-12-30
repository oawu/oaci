<div>Total:<?php echo $total;?></div>
<a href="<?php echo Restful::add ();?>">新增</a>

<hr>

<table border='1'>
  <thead>
    <tr>
      <th>ID</th>
      <th>標題</th>
      <th>編輯</th>
    </tr>
  </thead>
  <tbody>
<?php foreach ($tags as $tag) { ?>
        <tr>
          <td><?php echo $tag->id;?></td>
          <td><?php echo $tag->name;?></td>
          <td>
            <a href="<?php echo Restful::show ($tag);?>">檢視</a>
            <a href="<?php echo Restful::edit ($tag);?>">修改</a>
            <a href="<?php echo Restful::destroy ($tag);?>" data-method='delete'>刪除</a>
          </td>
        </tr>
<?php }?>
  </tbody>
</table>

<?php echo implode (' ', $pgn);?>