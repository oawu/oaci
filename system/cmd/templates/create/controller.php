{<{<{ if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class <?php echo ucfirst ($name);?> extends <?php echo ucfirst ($action);?>_controller {
<?php
  if ($methods) {
    foreach ($methods as $method) { ?>

  public function <?php echo $method;?> () {
    $this->load_view ();
  }
<?php
    }
  } ?>
}
