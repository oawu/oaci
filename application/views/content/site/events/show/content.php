<div class='container'>
<?php echo render_cell ('demo_cell', 'main_menu', array ()); ?>
  <a class='list' href='<?php echo base_url (array ('events', 'index'));?>'>列表</a>

  <table class='table-form'>
    <tbody>
      <tr>
        <th>編號</th>
        <td>
          <?php echo $event->id;?>
        </td>
      </tr>
      <tr>
        <th>標題</th>
        <td>
          <?php echo $event->title;?>
        </td>
      </tr>
      <tr>
        <th>資訊</th>
        <td>
          <?php echo nl2br ($event->info);?>
        </td>
      </tr>
      <tr>
        <th>封面</th>
        <td>
          <?php echo img ($event->cover->url ('100w'));?>
        </td>
      </tr>
      <tr>
        <th>標籤</th>
        <td>
    <?php if ($event->tags) { ?>
            <div class='units'>
        <?php foreach ($event->tags as $tag) { ?>
                <a class='unit' href='<?php echo base_url (array ('tags', 'show', $tag->id));?>'><?php echo $tag->name;?></a>
        <?php } ?>
            </div>
    <?php } else { ?>
            沒任何標簽。
    <?php } ?>
        </td>
      </tr>
      <tr>
        <th>參與者</th>
        <td>
    <?php if ($event->attendees) { ?>
            <div class='units'>
        <?php foreach ($event->attendees as $attendee) { ?>
                <div class='unit'><?php echo $attendee->name;?></div>
        <?php } ?>
            </div>
    <?php } else { ?>
            沒任何參與者。
    <?php } ?>
        </td>
      </tr>
    </tbody>
  </table>
</div>
