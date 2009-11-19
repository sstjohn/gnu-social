$(document).ready(function() {
     var mapstraction = new mxn.Mapstraction("map_canvas", _provider);

     var minLat = 181.0;
     var maxLat = -181.0;
     var minLon = 181.0;
     var maxLon = -181.0;

     for (var i in _notices)
     {
          var n = _notices[i];

          var lat = n['geo']['coordinates'][0];
          var lon = n['geo']['coordinates'][1];

          if (lat < minLat) {
               minLat = lat;
          }

          if (lat > maxLat) {
               maxLat = lat;
          }

          if (lon < minLon) {
               minLon = lon;
          }

          if (lon > maxLon) {
               maxLon = lon;
          }
     }

     var myPoint = new mxn.LatLonPoint(minLat + Math.abs(maxLat - minLat)/2,
                                       minLon + Math.abs(maxLon - minLon)/2);

     // display the map centered on a latitude and longitude (Google zoom levels)

     mapstraction.setCenterAndZoom(myPoint, 9);
});
