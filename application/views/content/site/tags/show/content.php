<div class='container'>
<?php echo render_cell ('demo_cell', 'main_menu', array ()); ?>
  <a class='list' href='<?php echo base_url (array ('tags', 'index'));?>'>列表</a>

  <table class='table-form'>
    <tbody>
      <tr>
        <th>編號</th>
        <td>
          <?php echo $tag->id;?>
        </td>
      </tr>
      <tr>
        <th>名稱</th>
        <td>
          <?php echo $tag->name;?>
        </td>
      </tr>
      <tr>
        <th>活動</th>
        <td>
    <?php if ($tag->events) { ?>
            <div class='units'>
        <?php foreach ($tag->events as $event) { ?>
                <a class='unit' href='<?php echo base_url (array ('events', 'show', $event->id));?>'>
                  <div class='id'><?php echo $event->id;?></div>
                  <?php echo $event->title;?>
                </a>
        <?php } ?>
            </div>
    <?php } else { ?>
            沒任何活動。
    <?php } ?>
        </td>
      </tr>
    </tbody>
  </table>
</div>
