<!DOCTYPE html>
<html lang="tw">
  <head>
    <meta http-equiv="Content-Language" content="zh-tw" />
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui" />

    <title></title>

    <link href="<?php echo asset ('/assets/css/public.css');?>" rel="stylesheet" type="text/css" />

    <script src="<?php echo asset ('/assets/js/jquery_v1.10.2/jquery-1.10.2.min.js');?>" language="javascript" type="text/javascript" ></script>
    <script src="<?php echo asset ('/assets/js/jquery-rails_d2015_03_09/jquery_ujs.js');?>" language="javascript" type="text/javascript" ></script>

  </head>
  <body lang="zh-tw">
    
    <div id='container'>
      <aside id='aside'>
        <a href="<?php echo $url = RestfulUrl::url ('tags@index');?>"<?php echo $current_url == $url ? ' class="a"' : '';?>>標籤</a>
        <a href="<?php echo $url = RestfulUrl::url ('articles@index');?>"<?php echo $current_url == $url ? ' class="a"' : '';?>>文章</a>
      </aside>
      <main id='main'>
        <?php echo $content;?>
      </main>
    </div>

  </body>
</html>