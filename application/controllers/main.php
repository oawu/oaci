<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2015 OA Wu Design
 */

class Main extends Site_controller {

  public function __construct () {
    parent::__construct ();
  }

  public function stores () {
    $northEast = $this->input_post ('northEast');
    $SouthWest = $this->input_post ('SouthWest');

    $stores = array_map (function ($s) {
      return array (
          'id' => $s->id,
          'name' => $s->name,
          'lat' => $s->latitude,
          'lng' => $s->longitude,
        );
    }, Store::all (array ('order' => 'rand()', 'conditions' => array (
      '(latitude BETWEEN ? AND ?) AND (longitude BETWEEN ? AND ?)', $SouthWest['latitude'], $northEast['latitude'], $SouthWest['longitude'], $northEast['longitude']
      ))));

    return $this->output_json (array ('status' => true, 'stores' => $stores));
  }

  public function stores2 () {
    $this->load->library ('ElasticaSearch');

    $northEast = $this->input_post ('northEast');
    $SouthWest = $this->input_post ('SouthWest');
    $center = $this->input_post ('center');

    $e = ElasticaSearch::find ('stores', array (), array (
        'must' => array ('name' => '咖啡'),
        'select' => array ('id', 'name', 'lat', 'lng'),
        'range' => array ('lat' => array ('from' => $SouthWest['latitude'], 'to' => $northEast['latitude']),
                          'lng' => array ('from' => $SouthWest['longitude'], 'to' => $northEast['longitude'])),
        'script_fields' => array ('distance' => array (
            'script' => "6371*acos(cos(toRadians(my_lat))*cos(toRadians(doc['lat'].value))*cos(toRadians(doc['lng'].value)-toRadians(my_lng))+sin(toRadians(my_lat))*sin(toRadians(doc['lat'].value)))",
            'params' => array ('my_lat' => (double)($center['latitude']), 'my_lng' => (double)($center['longitude']))
          ))
      ));

    return $this->output_json (array ('status' => true, 'stores' => $e));
  }
  public function index () {
    $this->load->library ('Scws');

    $a = Scws::explode ('在漫漫人生當中，你會遇到許多影響你人生價值觀的朋友或是情人。如果你交到值得信賴的朋友，那恭喜你的人生成功了一半；但如果你身邊的朋友或情人是以下類型的話，請燼速遠離這些人會比較好。');
    echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
    var_dump ($a);
    exit ();
    // $this->load_view ();










    // $e = ElasticaSearch::count ('stores', array (), array (
    //     'must' => array ('name' => '咖啡'),
    //     'select' => array ('id', 'name', 'lat', 'lng'),
    //     'sort' => array ('id' => 'asc'),
    //     // 'must_not' => array ('name' => '賢'),
    //   ));
    // echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
    // var_dump ($e);
    // exit ();
    // $this->load_view ($e);

//     $a = ElasticaSearch::find ('stores', array (), array (
//         'range' => array ('id' => array ('from' => 1, 'to' => 100)),
//         // 'offset' => 0,
//         // 'limit' => 1000,
//         // 'select' => array ('id', 'name', '_score'),
//         'sort' => array ('id' => 'asc'),
//         // 'fields' => array ('name'),
//         // 'select' => array ('id', 'name'),
//         // 'must' => array ('id' => 5, 'name' => 'oa'),
//         // 'must_not' => array ('name' => '賢'),
//       ));
// echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
// var_dump (($a));
// exit ();

    // echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
    // var_dump (ElasticaSearch::finds ('stores', '咖啡', array (
    //   'fields' => array ('name'),
    //     'select' => array ('name')
    //   )));
    // exit ();
    // ElasticaSearch::clean_index ();
    // // ElasticaSearch::create_index ();
    // // $a = ElasticaSearch::count ('stores', '咖啡', array(
    // //   'fields' => array('name'),
    // //   'select' => array('name'),
    // //   ));
    // // echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
    // // var_dump ($a);
    // // exit ();
    // // ElasticaSearch::build ();
    // foreach (Store::all () as $store)
    //   ElasticaSearch::create ('stores', array (
        // array('id' => $store->id, 'name' => $store->name, 'lat' => $store->latitude, 'lng' => $store->longitude)
    //   ));


    // ElasticaSearch::create ('user', array (
    //     array('id' => 2, 'name' => '吳歐Ａ')
    //   ));

    // ElasticaSearch::delete ('user', array ('1'));

    // $a = ElasticaSearch::finds ('user', '吳', array (
    //     'offset' => 0,
    //     'limit' => 10,
    //     'select' => array ('id', 'name'),
    //     'sort' => array ('id' => 'asc'),
    //     'fields' => array ('name'),
    //     'select' => array ('id', 'name'),
    //     // 'must' => array ('id' => 5, 'name' => 'oa'),
    //     'must_not' => array ('name' => '賢'),
    //   ));
    // $a = Product::count ();
    // $this->load->model ('product_model');
    // $p = $this->product_model->all ();

    // foreach ($p as $k => $v) {

    //   ElasticaSearch::create ('product', array (
    //       array('id' => $v['storeId'], 'name' => $v['name'], 'lat' => $v['latitude'], 'lng' => $v['longitude'])
    //     ));
    // }

//     $this->load->library ('Scws');

//     $data = Scws::explode ('8/23北市路口車禍 需行車紀錄或目擊者');
// echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
// echo '<br/>涼風有訊 秋月無邊 虧我思嬌的情緒好比度日如年<br/>雖然我不是玉樹臨風 瀟灑倜儻 但我有廣闊的胸襟加強健的臂彎<br/>';
// echo '<br/>= tags =========================<br/><br/>';
// foreach ($data as $key => $value) {
//   echo $value . '<br/>';
// }
// exit ();
  }
}
