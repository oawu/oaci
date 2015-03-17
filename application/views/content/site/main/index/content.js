/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2015 OA Wu Design
 */

$(function () {
  var left = 0;
  var d4_unit_width = 1000;

  $('.time_line').mousewheel (function (e) {
    left = Math.abs (e.deltaX) > Math.abs (e.deltaY) ? -e.deltaX : e.deltaY;

    if ((parseFloat($(this).find ('.time_units').css ('left')) + left > 0) || ((parseFloat($(this).find ('.time_units').css ('left')) + left) < (0 - (d4_unit_width * (5 - 1)))))
      left = 0;

    $(this).find ('.time_units').css ({'left': '+=' + left});
    return false;
  });

});