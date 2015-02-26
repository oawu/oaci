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
    $message = identity ()->get_session ('_flash_message', true);
    $events = Event::all ();
    $this->load_view (array ('events' => $events, 'message' => $message));
  }

  public function show () {
    $this->load_view (null);
  }

  public function add () {
    $message = identity ()->get_session ('_flash_message', true);
    $this->load_view (array ('message' => $message));
  }

  public function create () {
    $title = trim ($this->input_post ('title'));
    $info  = trim ($this->input_post ('info'));
    $cover = $this->input_post ('cover', true, true);

    if (verifyCreateOrm ($event = Event::create (array ('title' => $title, 'info' => $info, 'cover' => ''))) && $event->cover->put ($cover)) {
      identity ()->set_session ('_flash_message', '新增成功!', true);
      redirect (array ($this->get_class (), 'index'), 'refresh');
    } else {
      identity ()->set_session ('_flash_message', '新增失敗!', true);
      redirect (array ($this->get_class (), 'add'), 'refresh');
    }
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
