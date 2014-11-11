$(function(){
    var startDateTextBox = $('#datetime_from');
    var endDateTextBox = $('#datetime_end');

    startDateTextBox.datetimepicker ({ 
      stepHour: 1, firstDay: 0, stepMinute: 1, stepSecond: 1, changeYear: true, changeMonth: true, dateFormat: 'yy-mm-dd', timeFormat: 'HH:mm:ss', yearRange: '1901:2030',
      onClose: function (dateText, inst) {
        if (endDateTextBox.val () != '') { var testStartDate = startDateTextBox.datetimepicker ('getDate'), testEndDate = endDateTextBox.datetimepicker ('getDate'); if (testStartDate > testEndDate) endDateTextBox.datetimepicker ('setDate', testStartDate); }
        else { endDateTextBox.val (dateText); }
      },
      onSelect: function (selectedDateTime) { endDateTextBox.datetimepicker ('option', 'minDate', startDateTextBox.datetimepicker ('getDate') ); }
    });
    endDateTextBox.datetimepicker ({ 
      firstDay: 0, stepHour: 1, stepMinute: 1, stepSecond: 1, changeYear: true, changeMonth: true, dateFormat: 'yy-mm-dd', yearRange: '1901:2030', timeFormat: 'HH:mm:ss',
      onClose: function (dateText, inst) {
        if (startDateTextBox.val () != '') { var testStartDate = startDateTextBox.datetimepicker ('getDate'), testEndDate = endDateTextBox.datetimepicker ('getDate'); if (testStartDate > testEndDate) startDateTextBox.datetimepicker ('setDate', testEndDate); }
        else { startDateTextBox.val (dateText); }
      },
      onSelect: function (selectedDateTime) { startDateTextBox.datetimepicker ('option', 'maxDate', endDateTextBox.datetimepicker ('getDate') ); }
    });

    $('#sortable').sortable ({
    handle: '.handle',
    stop: function (event, ui) {
        var length = $(this).children ('.item').length;
        if ($(this).data ('order') && ($(this).data ('order') == 'asc')) var sorts = $.makeArray ($(this).children ('.item').map (function (i, t) { return $(t).data ('id') + '_' + (i + 1); }));
        else var sorts = $.makeArray ($(this).children ('.item').map (function (i, t) { return $(t).data ('id') + '_' + ((length - i) + 1); }));
        if (sorts.length) {
          $.ajax ({
            url: $('#sort_url').val (),
            data: { sorts: sorts },
            async: true, cache: false, dataType: 'json', type: 'POST',
            beforeSend: function () {}
          })
          .done (function (result) { $.jGrowl(result.message, {theme: 'j_growl', easing: "easeInExpo"}); })
          .fail (function (result) { ajaxError (result); })
          .complete (function (result) {});
        }
    }}).disableSelection ();

  $('.created_at, .updated_at').timeago ();

  $('div.bs-sidebar ul.list-group li.list-group-item.pointer').click (function () { window.location.assign ($(this).data ('url')); }).each (function (i, t) {if (window.location.href.indexOf ($(this).data ('url')) != -1) $(this).addClass ('active'); });
  $('#target_index_list_search').click (function () { if ($('#search_form').is (':visible')) { $(this).removeClass ('active'); $('#search_form').hide ('blind'); } else { $(this).addClass ('active'); $('#search_form').show ('blind'); } });
});









