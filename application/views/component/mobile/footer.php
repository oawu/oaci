<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ($footer_list) { ?>
    <div class='description'>
      <div class='divider'></div>
      <div class='content top'>OA's Blog © 2014</div>
    </div>

    <div class='link_groups'>
<?php if ($footer_list) {
        foreach ($footer_list as $title => $footers) { ?>
          <div class='link_group'>
            <div class='group_title'>
              <?php echo $title; ?>
            </div>
            <div class='link_list'>
        <?php if (count ($footers)) {
                foreach ($footers as $footer) { ?>
                  <a href='<?php echo $footer['src']; ?>' target='_blank'><?php echo $footer['name']; ?></a>
          <?php }
              } ?>
            </div>
          </div>
  <?php }
      } ?>
    </div>

    <div class='description'>
      <div class='divider'></div>
      <div class='content bottom'>如有 <u>相關問題</u> 歡迎來信 <key>comdan66@gmail.com</key> 或至 <a href='https://www.facebook.com/comdan66' target='_blank'>作者臉書</a> 留言。</div>
    </div>
<?php
}