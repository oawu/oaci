/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

Array.prototype.column = function (k) {
  return this.map (function (t) { return k ? eval ("t." + k) : t; });
};
Array.prototype.diff = function (a, k) {
  return this.filter (function (i) { return a.column (k).indexOf (eval ("i." + k)) < 0; });
};
Array.prototype.max = function (k) {
  return Math.max.apply (null, this.column (k));
};
Array.prototype.min = function (k) {
  return Math.min.apply (null, this.column (k));
};

window.ajaxError = function (result) {
  console.error (result.responseText);
};

$(function () {
});