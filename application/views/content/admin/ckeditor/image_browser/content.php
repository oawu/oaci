<div id='tabs'>
<?php 
  foreach ($types as $k => $type) { ?>
    <a href='<?php echo $type['u'];?>'<?php echo ($type['a'] && ($now = $k)) ? ' class="a"' : '';?>><?php echo $type['t'];?></a>
<?php
  } ?>
</div>

<div id='imgs' class='<?php echo $now . ($objs ? '' : ' e');?>'>
<?php
  foreach ($objs as $obj) { ?>
    <div data-url='<?php echo $obj->name->url ('h800');?>'>
      <div><img src='<?php echo $obj->user->avatar ();?>'></div>
      <img src='<?php echo $obj->name->url ('h800');?>'>
      <time datetime='<?php echo $obj->created_at->format ('Y-m-d H:i:s');?>'><?php echo $obj->created_at->format ('Y-m-d H:i:s');?></span>
    </div>
  <?php
  } ?>
</div>

<div class='pagination'><?php echo $pagination;?></div>