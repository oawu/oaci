var showAlert = function (title, content, action, isShow) {
  if (!($dialog = $('body').children ('#_alert')).length) $dialog = $('<div />').attr ('id', '_alert').appendTo ($('body')).OA_Dialog ({ openEffect: 'explode', closeEffect: 'explode'});

  title   = (typeof title   === 'undefined') ? '':title;
  content = (typeof content === 'undefined') ? '':content;
  action  = (typeof action  === 'undefined') ? function (){}:action;
  isShow  = (typeof isShow  === 'undefined') ? true:isShow;

  $dialog.OA_Dialog ('option', 'title', title)
         .OA_Dialog ('option', 'content', content)
         .OA_Dialog ('option', 'buttons', {'確定':action});

  isShow && $dialog.OA_Dialog ('open');
  return $dialog;
}

var showWait = function (title, content, isShow) {
  if (!($dialog = $('body').children ('#_wait')).length) $dialog = $('<div />').attr ('id', '_wait').appendTo ($('body')).OA_Dialog ({ openEffect: 'explode', closeEffect: 'explode'});
  
  title   = (typeof title   === 'undefined') ? '':title;
  content = (typeof content === 'undefined') ? '':content;
  isShow  = (typeof isShow  === 'undefined') ? true:isShow;

  $dialog.OA_Dialog ('option', 'title', title)
         .OA_Dialog ('option', 'content', content);

  isShow && $dialog.OA_Dialog ('open');
  return $dialog;
}

var showConfirm = function (title, content, action, closeAction, isShow) {
  if (!($dialog = $('body').children ('#_confirm')).length) $dialog = $('<div />').attr ('id', '_confirm').appendTo ($('body')).OA_Dialog ({ openEffect: 'explode', closeEffect: 'explode'});
  
  title       = (typeof title       === 'undefined') ? 'Title':title;
  content     = (typeof content     === 'undefined') ? 'Content':content;
  action      = (typeof action      === 'undefined') ? function (){}:action;
  closeAction = (typeof closeAction === 'undefined') ? function (){}:closeAction;
  isShow      = (typeof isShow      === 'undefined') ? true:isShow;

  var callbacks = $.Callbacks()
  callbacks.add (closeAction);
  callbacks.add (function () {$(this).OA_Dialog ('close');});

  $dialog.OA_Dialog ('option', 'title', title)
         .OA_Dialog ('option', 'content', content)
         .OA_Dialog ('option', 'buttons', { '取消': callbacks.fire, '確定': action});

  isShow && $dialog.OA_Dialog ('open');
  return $dialog;
}

var ajaxError = function (result) {
  console.info (result.responseText);
  showAlert ('Error!', 'The Ajax response error! Please inform the website admin this issue!', function () {location.reload();});
}

var base_url = function () {
  params = new Array ();
  for (var i = 0; i < arguments.length; i++) params.push (arguments[i]);
  params.unshift ('http://' + document.domain);
  return jQuery.map (params, function (t, i) { return t.length != 0 ? t : null; }).join ('/');
}

var sprintf = function () {
  var i = 0, a, f = arguments[i++], o = [], m, p, c, x, s = '';
  while (f) {
    if (m = /^[^\x25]+/.exec(f)) {
      o.push(m[0]);
    }
    else if (m = /^\x25{2}/.exec(f)) {
      o.push('%');
    }
    else if (m = /^\x25(?:(\d+)\$)?(\+)?(0|'[^$])?(-)?(\d+)?(?:\.(\d+))?([b-fosuxX])/.exec(f)) {
      if (((a = arguments[m[1] || i++]) == null) || (a == undefined)) {
        throw('Too few arguments.');
      }
      if (/[^s]/.test(m[7]) && (typeof(a) != 'number')) {
        throw('Expecting number but found ' + typeof(a));
      }
      switch (m[7]) {
        case 'b': a = a.toString(2); break;
        case 'c': a = String.fromCharCode(a); break;
        case 'd': a = parseInt(a); break;
        case 'e': a = m[6] ? a.toExponential(m[6]) : a.toExponential(); break;
        case 'f': a = m[6] ? parseFloat(a).toFixed(m[6]) : parseFloat(a); break;
        case 'o': a = a.toString(8); break;
        case 's': a = ((a = String(a)) && m[6] ? a.substring(0, m[6]) : a); break;
        case 'u': a = Math.abs(a); break;
        case 'x': a = a.toString(16); break;
        case 'X': a = a.toString(16).toUpperCase(); break;
      }
      a = (/[def]/.test(m[7]) && m[2] && a >= 0 ? '+'+ a : a);
      c = m[3] ? m[3] == '0' ? '0' : m[3].charAt(1) : ' ';
      x = m[5] - String(a).length - s.length;
      p = m[5] ? str_repeat(c, x) : '';
      o.push(s + (m[4] ? a + p : p + a));
    }
    else {
      throw('Huh ?!');
    }
    f = f.substring(m[0].length);
  }
  return o.join('');
}

$(function(){
});