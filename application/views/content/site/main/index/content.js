/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2015 OA Wu Design
 */

$(function () {
  var left = 0;
  var d4_unit_count = 2;
  var d4_unit_width = 1000;


  function load () {

    $.ajax ({
      url: $('#get_units_url').val (),
      data: { next_id: 1 },
      async: true, cache: false, dataType: 'json', type: 'POST',
      beforeSend: function () {

      }
    })
    .done (function (result) {
console.info (result);
    })
    .fail (function (result) { ajaxError (result); })
    .complete (function (result) { });
  }


  function move () {
    $('.time_line').find ('.time_units').css ({'left': '+=' + left});
  }

  $('.time_line').mousewheel (function (e) {
    left = Math.abs (e.deltaX) > Math.abs (e.deltaY) ? -e.deltaX : e.deltaY;

    if (parseFloat($(this).find ('.time_units').css ('left')) + left > 0)
      left = 0;

    if ((parseFloat($(this).find ('.time_units').css ('left')) + left) < (0 - (d4_unit_width * (d4_unit_count - 1)))) {
      load ();
      left = 0;
    }

    move ();
    return false;
  });

  move ();
});