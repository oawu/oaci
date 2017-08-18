<div id='box'>
  <header>
    <div class='logo'><?php echo Cfg::setting ('company', 'char');?></div>
    <div class='title'>
      <h1><?php echo Cfg::setting ('company', 'name');?>管理系統</h1>
      <p>Hello 歡迎使用<?php echo Cfg::setting ('company', 'name');?>管理系統！</p>
    </div>
  </header>
  
  <span><?php echo Session::getData ('_fd', true);?></span>

  <a href='<?php echo Fb::loginUrl ('platform', 'fb_sign_in', 'admin', 'my');?>' class='facebook-login'>使用 Facebook 登入</a>

  <div class='or'>or</div>

  <form action='<?php echo base_url ('platform', 'ap_sign_in', 'admin', 'my');?>' method='post'>
    <input type='text' name='account' placeholder='請輸入帳號' value='<?php echo isset ($posts['account']) && $posts['account'] ? $posts['account'] : '';?>' />
    <input type='password' name='password' placeholder='請輸入密碼' value='<?php echo isset ($posts['password']) && $posts['password'] ? $posts['password'] : '';?>' />
    <button type='submit'>使用帳密登入</button>
  </form>

  <footer>© <?php echo date ('Y');?> <?php echo Cfg::setting ('company', 'domain');?></footer>
</div>