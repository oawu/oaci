<!DOCTYPE html>
<html lang="tw">
  <head>
    <meta http-equiv="Content-Language" content="zh-tw" />
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui" />

    <title>後台系統</title>

    <?php echo $asset->renderCSS ();?>
    <?php echo $asset->renderJS ();?>

  </head>
  <body lang="zh-tw">
    
    <main id='main'>
      <header id='main-header'>
        <a id='hamburger' class='icon-01'></a>
        <nav><b>文章系統</b></nav>
        <!-- <nav><b>文章系統</b><span>文章列表</span></nav> -->
        <a href='<?php echo URL::base ('logout');?>' class='icon-02'></a>
      </header>

      <div class='flash <?php echo $flash['type'];?>'><?php echo $flash['msg'];?></div>

      <div id='container'>
  <?php echo isset ($content) ? $content : ''; ?>
      </div>

    </main>

    <div id='menu'>
      <header id='menu-header'>
        <a href='<?php echo URL::base ();?>' class='icon-15'></a>
        <span>後台管理系統</span>
      </header>

      <div id='menu-user'>
        <figure class='_ic'>
          <img src="https://cdn.pixabay.com/photo/2017/07/09/11/29/sun-flower-2486721_1280.jpg">
        </figure>

        <div>
          <span>Hi, 您好!</span>
          <b>管理員</b>
        </div>
      </div>

      <div id='menu-main'>
        <div>
          <span class='icon-14' data-cnt='<?php echo $acnt = Article::count (Where::create ('status = ?', Article::STATUS_OFF));?>' data-cntlabel='article'>管理區</span>
          <div>
            <a href="" class='icon-21'>後台首頁</a>
            <a href="<?php echo $url = RestfulUrl::url ('admin/tags@index');?>" class='icon-42<?php echo isset ($current_url) && $url === $current_url ? ' active' : '';?>'>標籤</a>
            <a data-cnt='<?php echo $acnt;?>' data-cntlabel='article' href="<?php echo $url = RestfulUrl::url ('admin/articles@index');?>" class='icon-22<?php echo isset ($current_url) && $url === $current_url ? ' active' : '';?>'>文章</a>
          </div>
        </div>

        <div>
          <span class='icon-14'>管理區</span>
          <div>
            <a href="" class='icon-21'>後台首頁</a>
            <a href="" class='icon-20'>Banner 上稿</a>
            <a href="" class='icon-22'>文章 上稿</a>
          </div>
        </div>
      </div>
    </div>

    <footer id='footer'><span>後台版型設計 by </span><a href='https://www.ioa.tw/' target='_blank'>OA Wu</a></footer>

  </body>
</html>
