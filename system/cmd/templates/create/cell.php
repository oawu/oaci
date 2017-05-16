{<{<{ if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class <?php echo ucfirst ($file_name);?> extends Cell_Controller {
<?php
  if ($methods) {
    foreach ($methods as $method) { ?>

  /* render_cell ('<?php echo $file_name;?>', '<?php echo $method;?>', var1, ..); */
  // public function <?php echo $method_prefix . $method;?> () {
  //   return array ('time' => 60 * 60, 'key' => null);
  // }
  public function <?php echo $method;?> () {
    return $this->load_view ();
  }
<?php
    }
  } ?>
}