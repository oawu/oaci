<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2015 OA Wu Design
 */

class Main extends Site_controller {

  public function __construct () {
    parent::__construct ();
  }

  public function index () {

    // $params = array (
    //   'access_key' => '',
    //   'secret_key' => '',
    //   'use_ssl' => false,
    //   'verify_peer' => true
    //   );
    // $this->load->library ('S3', $params);
    // S3::setAuth ($params["access_key"], $params["secret_key"]);
    // echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
    // var_dump (S3::listDistributions ());
    // exit ();;





// $fileName = FCPATH . 'temp/S__7880806.jpg';

// $bucket = 'ioa';
// $uri = sprintf("123/234");
// $input = S3::inputFile ($fileName);

// $put = false;
// if ($input) {
//   $put = S3::putObject(
//     $input,
//     $bucket,
//     $uri,
//     S3::ACL_PUBLIC_READ,
//     array(),
//     array( // Custom $requestHeaders
//       "Cache-Control" => "max-age=315360000",
//       "Expires" => gmdate("D, d M Y H:i:s T", strtotime("+5 years"))
//     )
//   );
// }
  // echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
  // var_dump ($put);
  // exit ();
    // new S3 ();
    // $this->load_view (null);
    // $event = Event::create (array ('title' => '', 'cover' => '', 'info' => ''));
    
    // $fileName = FCPATH . 'temp/S__7880806.jpg';
    $file = $this->input_post ('file', true);

    $event = Event::find (1);
    echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
    var_dump ($event->cover->put ($file));
    exit ();;
  }
  public function x () {
    echo "<form action='" . base_url ('main', 'index') . "' method='post' enctype='multipart/form-data'>
            <input type='file' name='file' />
            <button type='submit'>submit</button>
          </form>";
  }
  public function o () {
    // echo "<form action='" . base_url ('main', 'index') . "' method='post' enctype='multipart/form-data'>
    //         <input type='file' name='file' />
    //         <button type='submit'>submit</button>
    //       </form>";

    $params = array (
      'access_key' => '',
      'secret_key' => '',
      'use_ssl' => false,
      'verify_peer' => true
      );
    $this->load->library ('S3', $params);
    // echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
    // var_dump (S3::listDistributions ());
    // exit ();;
    echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
    var_dump (S3::getObject ('ioa', '123/234'));
    exit ();;
  }
}
