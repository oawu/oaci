{<{<{ if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class <?php echo $name;?> extends OrmImageUploader {

  public function getVersions () {
    return array (
        '' => array (),
        '100w' => array ('resize', 100, 100, 'width'),
        '120x80c' => array ('adaptiveResizeQuadrant', 120, 80, 'c')
      );
  }
}