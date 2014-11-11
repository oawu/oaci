$(function () {
  if ($('#body #footer .link_groups .link_group').length) {
    $('#body #footer .link_groups').css ({'width': $('#body #footer .link_groups .link_group').length < 4 ? (($('#body #footer .link_groups .link_group').length * ((parseFloat ($('#body #footer .link_groups .link_group').css ('margin-left')) + parseFloat ($('#body #footer .link_groups .link_group').css ('margin-right')) + parseFloat ($('#body #footer .link_groups .link_group').css ('width'))))) + 'px') : '100%'});
    var link_groups_masonry = new Masonry ('#body #footer .link_groups', { itemSelector: '.link_group', columnWidth: 1, transitionDuration: '0.3s', visibleStyle: { opacity: 1, transform: 'none' }}); 
  }

  if ($('#body #header .header_padding .avatar img').length)
    $('#body #header .header_padding .avatar').imgLiquid ({verticalAlign: "center"});
  $('#body #header .header_padding .search .button').click (function () {});
});
