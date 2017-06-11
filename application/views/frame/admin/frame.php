<!DOCTYPE html>
<html lang="zh">
  <head>
    <?php echo isset ($meta_list) ? $meta_list : ''; ?>

    <title><?php echo isset ($title) ? $title : ''; ?></title>

<?php echo isset ($css_list) ? $css_list : ''; ?>

<?php echo isset ($js_list) ? $js_list : ''; ?>

  </head>
  <body lang="zh-tw">
    <?php echo isset ($hidden_list) ? $hidden_list : ''; ?>

    <input type='checkbox' class='hckb' id='menu_ckb' />
    <input type='checkbox' class='hckb' id='user_ckb' />

    <header id='header'>
      <div class='logo'>
        <span>
          <i class='icon-grin'></i>
        </span>
        <span>主標題 - 管理後台</span>
      </div>
      <div class='midle'>
        <label class='icon-menu' for='menu_ckb'></label>
      </div>
      <div class='avatar' news>
        <label for='user_ckb' class='_ic'>
          <img src='<?php echo User::current ()->avatar ();?>' />
        </label>
      </div>
    </header>

    <div id='main'>
      <div>
        <label class='alert type1'><?php echo Session::getData ('_fi', true);?></label>
        <label class='alert type3'><?php echo Session::getData ('_fd', true);?></label>

        <?php echo isset ($content) ? $content : ''; ?>
      </div>
    </div>

    <div id='menu'>
      <header>
        <span class='icon-amused-face-closed-eyes'></span>
        <span>主標題</span>
      </header>

      <div class='group'>
        <span class='icon-home'>後台</span>
        <div>
          <a class='icon-home' href='user.html'>個人頁面</a>
          <a class='icon-home' href='user.html'>系統通知</a>
        </div>
      </div>

      <div class='group'>
        <span class='icon-file-text2'>文章系統</span>
        <div>
          <a class='icon-price-tags<?php echo ($url = base_url ('admin', 'article_tags')) && isset ($_url) && ($url == $_url) ? ' show' : '';?>' href='<?php echo $url;?>'>文章分類</a>
          <a class='icon-file-text2<?php echo ($url = base_url ('admin', 'articles')) && isset ($_url) && ($url == $_url) ? ' show' : '';?>' href='<?php echo $url;?>' href='user.html'>文章列表</a>
        </div>
      </div>

      <footer>© 2017 oaci.tw</footer>
    </div><label class='icon-cross' for='menu_ckb'></label>

    <div id='user'>
      <div>
        <span>Hi, <b><?php echo User::current ()->name;?></b> 您好。</span>
        <span>目前登入次數：<b><?php echo number_format (User::current ()->login_count);?></b>次</span>
        <span>上次登入：<time datetime='<?php echo User::current ()->logined_at->format ('Y-m-d H:i:s');?>'><?php echo User::current ()->logined_at->format ('Y-m-d H:i:s');?></time></span>
        <a href='' class='icon-notifications_active' data-count='10,000'>您有未讀訊息</a>
        <a href='<?php echo base_url ('logout');?>' class='icon-power'>登出</a>
      </div>
    </div><label for='user_ckb'></label>

    <div id='tip_texts'></div>

  </body>
</html>