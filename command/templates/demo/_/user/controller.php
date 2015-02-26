{<{<{ if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2015 OA Wu Design
 */

class <?php echo ucfirst ($name);?> extends <?php echo ucfirst ($action);?>_controller {

  public function __construct () {
    parent::__construct ();
  }

  public function index () {
    $users = User::all ();
    $this->load_view (array ('users' => $users));
  }

  public function show () {
    $this->load_view (null);
  }

  public function add () {
    $this->load_view (null);
  }

  public function create () {
    $this->load_view (null);
  }

  public function edit () {
    $this->load_view (null);
  }

  public function update () {
    $this->load_view (null);
  }

  public function destroy () {
    $this->load_view (null);
  }
}
