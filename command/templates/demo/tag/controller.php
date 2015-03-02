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

    if (!($title && $info && $cover)) {
      identity ()->set_session ('_flash_message', '輸入資訊有誤!', true);
      return redirect (array ($this->get_class (), 'add'), 'refresh');
    }

    if (verifyCreateOrm ($event = Event::create (array ('title' => $title, 'info' => $info, 'cover' => ''))) && $event->cover->put ($cover)) {
      identity ()->set_session ('_flash_message', '新增成功!', true);
      return redirect (array ($this->get_class (), 'index'), 'refresh');
    } else {
      identity ()->set_session ('_flash_message', '新增失敗!', true);
      return redirect (array ($this->get_class (), 'add'), 'refresh');
    }
  }

  public function edit ($id) {
    if (!$event = Event::find_by_id ($id))
      redirect (array ($this->get_class (), 'index'));

    $message = identity ()->get_session ('_flash_message', true);
    $this->load_view (array ('message' => $message, 'event' => $event));
  }

  public function update ($id) {
    if (!$event = Event::find_by_id ($id))
      redirect (array ($this->get_class (), 'index'));

    $title = trim ($this->input_post ('title'));
    $info  = trim ($this->input_post ('info'));
    $cover = $this->input_post ('cover', true, true);

    if (!($title && $info)) {
      identity ()->set_session ('_flash_message', '輸入資訊有誤!', true);
      return redirect (array ($this->get_class (), 'add'), 'refresh');
    }

    $event->title = $title;
    $event->info = $info;

    if ($event->save () && (!$cover || $event->cover->put ($cover))) {
      identity ()->set_session ('_flash_message', '修改成功!', true);
      return redirect (array ($this->get_class (), 'index'), 'refresh');
    } else {
      identity ()->set_session ('_flash_message', '修改失敗!', true);
      return redirect (array ($this->get_class (), 'add'), 'refresh');
    }
  }

  public function destroy ($id) {
    if (!$event = Event::find_by_id ($id))
      redirect (array ($this->get_class (), 'index'));

    if ($event->cover->cleanAllFiles () && $event->delete ())
      identity ()->set_session ('_flash_message', '刪除成功!', true);
    else
      identity ()->set_session ('_flash_message', '刪除失敗!', true);

    return redirect (array ($this->get_class (), 'index'), 'refresh');
  }
}
