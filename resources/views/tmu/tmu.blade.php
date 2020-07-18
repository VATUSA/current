<!DOCTYPE html>
<html>
<head>
  <title>{{$facname}} TMU Map</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.1.0/dist/leaflet.css"
        integrity="sha512-wcw6ts8Anuw10Mzh9Ytw4pylW8+NAD4ch3lqm9lzAsTxg0GFeJgoAtxuCLREZSC5lUXdVyo/7yfsqFjQ4S+aKw=="
        crossorigin="">
  <script src="https://unpkg.com/leaflet@1.1.0/dist/leaflet.js"
          integrity="sha512-mNqn2Wg7tSToJhvHcqfzLMU6J4mkOImSPTxVZAdo+lcPlk+GhZmYgACEe0x35K7YzW1zJ7XyJV/TT1MrdXvMcA=="
          crossorigin=""></script>
  <script src='https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/Leaflet.fullscreen.min.js'></script>
  <link href='https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/leaflet.fullscreen.css' rel='stylesheet' />
  <style>
    body {
      padding: 0;
      margin: 0;
    }

    html,
    body,
    #mapid {
      height: 100%;
      width: 100%;
    }

    .row1 {
      font-weight: bold;
    }

<?php
for ($i = 1 ; $i <= 360 ; $i++) {
    echo ".rotate-$i{transform: rotate(" . $i . "deg) !important; }";
}
?>
    .leaflet-div-icon {
      background: none;
      border: none;
    }

    .searchbox {
      width: 4rem;
      background: rgba(255,255,255,0.4);
      border: none;
      font-weight: 800;
      font-family: Verdana;
      color: #0000ff;
      text-shadow: 1px 1px #000000;
      font-size: 20pt;
      text-align: right;
    }
    ::-webkit-input-placeholder { /* Chrome */
      color: #0000ff;
    }

    .facilityLine {
      color: #ff0000;
    }

    .airstats {
      @if($dark)
      background-image: url('/img/tmu/providerdark.png');
      @else
      background-image: url('/img/tmu/provider.png');
      @endif
      width: 330px;
      height: 65px;
    }

    .toggledarklight:hover, .airstats:hover {
      cursor: pointer;
    }
  </style>
</head>
<body>
  <div id="mapid"></div>
  <script
  src="https://code.jquery.com/jquery-3.2.1.min.js"
  integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
  crossorigin="anonymous"></script>
  <script type="text/javascript">
    var default_color = "{{$default}}"
    var colors = {!! $colors !!}

    var mymap = L.map('mapid');
    var polyStyle = {
      @if($dark == true)
      "color": "#666666",
      @else
      "color": "black",
      @endif
      "weight": 2.5,
      "opacity": 1
    };
    @if($dark == true)
    var geomap = L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}.png', {
    @else
    var geomap = L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}.png', {
    @endif
	     attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a> &copy; <a href="http://cartodb.com/attributions">CartoDB</a>',
	      subdomains: 'abcd',
	       maxZoom: 19
       }).addTo(mymap);

    var nexrad = L.tileLayer('https://mesonet.agron.iastate.edu/cache/tile.py/1.0.0/nexrad-n0q-900913/{z}/{x}/{y}.png?ts={ts}', {
      tileSize: 256,
      opacity: 0.5,
      ts: function() { return Date.now(); }
    }).addTo(mymap)

    L.geoJSON({!! $coords_geoJSON !!}, { style: polyStyle, fill: false }).addTo(mymap);
    mymap.fitBounds([[{{$min[0]}}, {{$min[1]}}], [{{$max[0]}}, {{$max[1]}}]]);

    L.Control.Clock = L.Control.extend({
      options: {
        // topright, topleft, bottomleft, bottomright
        position: 'topright',
        placeholder: '{{$fac}}'
      },
      initialize: function (options /*{ data: {...}  }*/) {
        // constructor
        L.Util.setOptions(this, options);
      },
      onAdd: function (map) {
        // happens after added to map
        var container = L.DomUtil.create('div', 'search-container');
        this.input = L.DomUtil.create('input', 'searchbox', container);
        this.input.type = 'text';
        this.input.value = "{{$fac}}";
        //this.input.placeholder = this.options.placeholder;
        L.DomEvent.disableClickPropagation(container);
        return container;
      },
    });

    L.control.clock = function(id, options) {
      return new L.Control.Clock(id, options);
    }
    L.control.clock({}).addTo(mymap);

    /*
    L.Control.AirStats = L.Control.extend({
      options: {
        // topright, topleft, bottomleft, bottomright
        position: 'bottomleft'
      },
      initialize: function (options) {
        L.Util.setOptions(this, options);
      },
      onAdd: function (map) {
        // happens after added to map
        var container = L.DomUtil.create('div', 'airstats');

        L.DomEvent.disableClickPropagation(container);
        return container;
      },
    });
    L.control.airstats = function(id, options) {
      return new L.Control.AirStats(id, options);
    }
    L.control.airstats({}).addTo(mymap);
    */

    L.Control.DarkLight = L.Control.extend({
      options: {
        position: 'topleft'
      },
      onAdd: function(map) {
        var container = L.DomUtil.create('div','leaflet-bar leaflet-control leaflet-control-custom toggledarklight')

        @if($dark)
        container.style.backgroundColor = '#666666';
        container.style.backgroundImage = "url(/img/tmu/toggledark.png)"
        @else
        container.style.backgroundColor = 'white';
        container.style.backgroundImage = "url(/img/tmu/toggle.png)"
        @endif
        container.style.backgroundSize = "30px 30px";
        container.style.width = '30px';
        container.style.height = '30px';

        return container;
      }
    })

    L.control.darklight = function() {
      return new L.Control.DarkLight()
    }
    L.control.darklight().addTo(mymap);

    var planeLayer = new L.LayerGroup();
    planeLayer.addTo(mymap);

    function updateNEXRAD() {
      nexrad.redraw()
    }

    function updatePlanes() {
      $.ajax({
        method: "GET",
        url: "https://api.vatusa.net/planes",
        dataType: 'json'
      }).done(function(data){
        planeLayer.clearLayers();
        for (var i = 0 ; i < data.length ; i++) {
          if (data[i].spd > 30) {
            createPlane(data[i].lat, data[i].lon, data[i].hdg, data[i].callsign, data[i].type, data[i].dep, data[i].arr)
          }
        }
      }).fail(function(xhr, textstatus) {
        console.log(textstatus);
      })
    }

    function getColor(arr) {
      if (typeof colors[arr] !== 'undefined')
        return colors[arr]
      return default_color
    }

    function createPlane(lat, lon, hdg, cs, actype, dep, arr) {
      var color = getColor(arr)
      var myIcon = L.divIcon({
        html: '<img src="/img/tmu/planes/' + color + '.png" class="rotate-' + hdg + '">'
      })
      lat = parseFloat(lat)
      lon = parseFloat(lon)
      var marker = L.marker([lat, lon], {icon: myIcon}).bindPopup('<span class="row1">' + cs + "</span><br> \
        <span class=\"row2\">" + dep + " - " + arr +"</span><br> \
        <span class=\"row3\">" + actype + "</span>");
      marker.on('mouseover', function (e) {
        this.openPopup();
      });
      marker.on('mouseout', function (e) {
        this.closePopup();
      });
      this.planeLayer.addLayer(marker);
    }

    updatePlanes();
    setInterval(function() { updatePlanes(); }, 1 * 60 * 1000); // Update every minute
    setInterval(function() { updateNEXRAD(); }, 10 * 60 * 1000); // Update NEXRAD every 10 minutes

    //$('.airstats').click(function() { window.location = "https://www.airstats.org"; });
    $('.toggledarklight').click(function() {
      @if ($dark)
      window.location="/tmu/map/{{$fac}}"
      @else
      window.location="/tmu/map/{{$fac}}/dark"
      @endif
    })
  </script>
</body>
</html>
