/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2015 OA Wu Design
 */

$(function () {

  var radius = 500; // m

  var $map = $('#map');
  var _map = null;
  var _rectangle = null;
  var _markers = [];
  var _marker = null;


  function calcBounds(center,size){
    var n=google.maps.geometry.spherical.computeOffset(center,size/2,0).lat(),
        s=google.maps.geometry.spherical.computeOffset(center,size/2,180).lat(),
        e=google.maps.geometry.spherical.computeOffset(center,size/2,90).lng(),
        w=google.maps.geometry.spherical.computeOffset(center,size/2,270).lng();
    return  new google.maps.LatLngBounds(new google.maps.LatLng(s,w), new google.maps.LatLng(n,e));
  }

  function updateForm () {
    if (_map.ajax) return;

    if(!_map.markers) _map.markers = [];

    var northEast = _rectangle.getBounds().getNorthEast ();
    var southWest = _rectangle.getBounds().getSouthWest ();
    var center = {latitude: (northEast.lat () + southWest.lat ()) / 2, longitude: (northEast.lng () + southWest.lng ()) / 2};

    $.ajax ({
      url: '/main/stores2',
      data: { northEast: {latitude: northEast.lat (), longitude: northEast.lng ()},
              SouthWest: {latitude: southWest.lat (), longitude: southWest.lng ()},
              center: center
            },
      async: true, cache: false, dataType: 'json', type: 'POST',
      beforeSend: function () {
        $map.ajax = true;
      }
    })
    .done (function (result) {
      if (!result.status) return false;

      _map.markers.forEach (function (t) {
        t.markerWithLabel.setMap (null);
      });

      _map.markers = result.stores.map (function (t) {
        return {
          id: t.id,
          markerWithLabel: new MarkerWithLabel ({
            position: new google.maps.LatLng (t.lat, t.lng),
            draggable: false,
            map: _map,
            raiseOnDrag: false,
            clickable: true,
            labelContent: t.name + '<hr />' + parseInt (t.distance * 1000, 10) + '公尺',
            labelAnchor: new google.maps.Point (50, 0),
            labelClass: "marker_label",
            icon: '/resource/image/map/spotlight-poi-blue.png',
            initCallback: function (t) {}
          })
        };
      });

      // var deletes = _map.markers.diff (markers, 'id');
      // var adds = markers.diff (_map.markers, 'id');
      // var delete_ids = deletes.column ('id');
      // var add_ids = adds.column ('id');

      // deletes.map (function (t) {
      //   t.markerWithLabel.setMap (null);
      // });
      // adds.map (function (t) {
      //   t.markerWithLabel.setMap (_map);
      // });

      // _map.markers = _map.markers.filter (function (t) { return $.inArray (t.id, delete_ids) == -1; }).concat (markers.filter (function (t) { return $.inArray (t.id, add_ids) != -1; }));
      _marker.setPosition (new google.maps.LatLng (center.latitude, center.longitude));

      // result
      // _markers.push (new google.maps.Marker ({
      //   map: _map,
      //   draggable: true,
      //   position: new google.maps.LatLng (25.04, 121.55)
      // }));

    })
    .fail (function (result) { ajaxError (result); })
    .complete (function (result) {
      _map.ajax = false;
    });

  }

  function initialize () {
    var position = new google.maps.LatLng (25.04, 121.55);

    _map = new google.maps.Map ($map.get (0), {
        zoom: 15,
        zoomControl: true,
        scrollwheel: true,
        scaleControl: true,
        mapTypeControl: false,
        navigationControl: true,
        center: position,
        streetViewControl: false,
        disableDoubleClickZoom: true,
      });
// new google.maps.Circle({
//         strokeColor: '#FF0000',
//         strokeOpacity: 0.3,
//         strokeWeight: 2,
//         fillColor: '#FF0000',
//         fillOpacity: 0.15,
//         map: _map,
//         draggable: true,
//         center: position,
//         radius: 10
//       });

    _marker = new google.maps.Marker ({
        map: _map,
        draggable: true,
        position: position
      });

    _rectangle = new google.maps.Rectangle({
        strokeColor: '#FF0000',
        strokeOpacity: 0.3,
        strokeWeight: 2,
        fillColor: '#FF0000',
        fillOpacity: 0.15,
        map: _map,
        center: position,
        draggable: true,
        bounds: calcBounds (position, radius * 2)
      });

    google.maps.event.addListener (_rectangle, 'dragend', function () {
      updateForm ();
    });

    google.maps.event.addListener(_map, 'click', function (e) {
      _rectangle.setCenter (e.latLng);
      updateForm ();
    });

    google.maps.event.addListener (_rectangle, 'drag', function () {
      var northEast = _rectangle.getBounds().getNorthEast ();
      var southWest = _rectangle.getBounds().getSouthWest ();
      var center = {latitude: (northEast.lat () + southWest.lat ()) / 2, longitude: (northEast.lng () + southWest.lng ()) / 2};
      _marker.setPosition (new google.maps.LatLng (center.latitude, center.longitude));
    });

    updateForm ();

  }

  google.maps.event.addDomListener (window, 'load', initialize);
});