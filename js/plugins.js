// Avoid `console` errors in browsers that lack a console.
(function() {
    var method;
    var noop = function () {};
    var methods = [
        'assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 'error',
        'exception', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log',
        'markTimeline', 'profile', 'profileEnd', 'table', 'time', 'timeEnd',
        'timeline', 'timelineEnd', 'timeStamp', 'trace', 'warn'
    ];
    var length = methods.length;
    var console = (window.console = window.console || {});

    while (length--) {
        method = methods[length];

        // Only stub undefined methods.
        if (!console[method]) {
            console[method] = noop;
        }
    }
}());

// -------------------------------------------------------------
// Google Map Customization
// -------------------------------------------------------------

/*==========
    - GoogleMap
    ============*/

  var map;

  function initialize() {
    var myLatlng = new google.maps.LatLng(8.4870303, 4.5692353);
    var mapOptions = {
      zoom: 18,
      center: myLatlng,
      scrollwheel: false
    };
    map = new google.maps.Map(
      document.getElementById("map-canvas"),
      mapOptions
    );

    var marker = new google.maps.Marker({
      position: myLatlng,
      map: map,
      title: "Hello World!"
    });
  }

  google.maps.event.addDomListener(window, "load", initialize);