<!DOCTYPE html>
<html lang="en">
  <head>
    <?php echo isset ($meta) ? $meta:''; ?>
    <title><?php echo isset ($title) ? $title : ''; ?></title>

    <?php echo isset ($css) ? $css:''; ?>
    <?php echo isset ($javascript) ? $javascript:''; ?>
  </head>
  <body lang="zh-tw">
    <?php echo isset ($hidden) ? $hidden:'';?>

    <?php echo isset ($content) ? $content : '';?>
  </body>
</html>