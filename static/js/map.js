RouteMap = (function() {
	var map;
	var vector;
	var markers;
	var lineLayer;
	var p = new Array();
	var wgs84 = new OpenLayers.Projection("EPSG:4326");
	var positionUri;
	var label = new Array();
	var icons = new Array();
	var obj;

	// increase reload attempts 
	OpenLayers.IMAGE_RELOAD_ATTEMPTS = 3;

	OpenLayers.Control.Click = OpenLayers.Class(OpenLayers.Control, {
		defaultHandlerOptions: {
			'single': true,
			'double': false,
			'pixelTolerance': 0,
			'stopSingle': false,
			'stopDouble': false
		},

		initialize: function (options) {
			this.handlerOptions = OpenLayers.Util.extend({}, this.defaultHandlerOptions);
			OpenLayers.Control.prototype.initialize.apply(
			this, arguments);
			this.handler = new OpenLayers.Handler.Click(
			this, {
				'click': this.trigger
			}, this.handlerOptions);
		},

		trigger: function (e) {
			if (positionUri == undefined) return;
			var lonlat = map.getLonLatFromViewPortPx(e.xy);
			var llc = lonlat.clone();
			var size = new OpenLayers.Size(32, 37);
			var offset = new OpenLayers.Pixel(-(size.w / 2), -size.h);
			var icon = new OpenLayers.Icon(icons[positionUri], size, offset);
			if (p[positionUri] != undefined) {
				markers.removeMarker(p[positionUri]);
			}
			//else
			//{
			p[positionUri] = new OpenLayers.Marker(lonlat, icon);
			markers.addMarker(p[positionUri]);
			//}
			llc.transform(map.getProjectionObject(), wgs84);
			document.getElementById('loc_' + positionUri).innerHTML = Math.round(llc.lat * 1000000) / 1000000 + '/' + Math.round(llc.lon * 1000000) / 1000000;
			positionUri = undefined;
			document.getElementById('actionText').innerHTML = 'Please select an item...';
		}

	});


	function init(long1, lat1, long2, lat2) {
		var left = Math.min(long1, long2);
		var top = Math.min(lat1, lat2);
		var right = Math.max(long1, long2);
		var bottom = Math.max(lat1, lat2);
		
	
		var i = document.getElementById("maind");
		i.style.height = "450px";
		var maxExtent = new OpenLayers.Bounds(-20037508, -20037508, 20037508, 20037508),
			restrictedExtent = maxExtent.clone(),
			maxResolution = 156543.0339;

		var options = {
			projection: new OpenLayers.Projection("EPSG:900913"),
			displayProjection: new OpenLayers.Projection("EPSG:4326"),
			units: "m",
			numZoomLevels: 18,
			maxResolution: maxResolution,
			maxExtent: maxExtent,
			restrictedExtent: restrictedExtent
		};
		map = new OpenLayers.Map('map', options);

		var streetview = new OpenLayers.Layer.StreetView("OS StreetView (1:10000)");

		markers = new OpenLayers.Layer.Markers("Editable Markers");

		map.addLayers([streetview, markers]);
		
		var size = new OpenLayers.Size(32,37);
		var offset = new OpenLayers.Pixel(-(size.w/2), -size.h);
			

		//var size = new OpenLayers.Size(32, 37);
		//var offset = new OpenLayers.Pixel(-(size.w / 2), -size.h);

		if (!map.getCenter()) {
			var gb = new OpenLayers.Bounds(left, top, right, bottom);
			gb.transform(wgs84, map.getProjectionObject());
			map.zoomToExtent(gb);
			if (map.getZoom() < 6) map.zoomTo(6);
		}

		var click = new OpenLayers.Control.Click();
		map.addControl(click);
		click.activate();
		
        lineLayer = new OpenLayers.Layer.Vector("Line Layer"); 

        map.addLayer(lineLayer);                    
        map.addControl(new OpenLayers.Control.DrawFeature(lineLayer, OpenLayers.Handler.Path));         
        obj.onInit();  
        
		var icon1 = new OpenLayers.Icon('http://data.southampton.ac.uk/map-icons/Restaurants-and-Hotels/hotel_0star.png', size, offset);
		var icon2 = new OpenLayers.Icon('http://data.southampton.ac.uk/map-icons/Restaurants-and-Hotels/hostel_0star.png', size, offset);
		var p1 = new OpenLayers.LonLat(long1, lat1);
		var p2 = new OpenLayers.LonLat(long2, lat2);
		p1.transform(wgs84, map.getProjectionObject());
		p2.transform(wgs84, map.getProjectionObject());
		markers.addMarker(new OpenLayers.Marker(p1, icon1));
		markers.addMarker(new OpenLayers.Marker(p2, icon2));                 
		
	}


	function position(uri) {
		document.getElementById('actionText').innerHTML = 'Setting location of ' + label[uri];
		positionUri = uri;
	}
	
	obj = {
		getMap: function() { return map; },
		getProj: function() {return wgs84; },
		getLineLayer: function() { return lineLayer; },
		addMarker: function(long1, lat1, img) {
			var size = new OpenLayers.Size(32, 37);
			var offset = new OpenLayers.Pixel(-(size.w/2), -size.h);
			var icon = new OpenLayers.Icon(img, size, offset);
			var p1 = new OpenLayers.LonLat(long1, lat1);
			p1.transform(wgs84, map.getProjectionObject());
			markers.addMarker(new OpenLayers.Marker(p1, icon));
		},
		init: init,
		onInit: function() {},
		addLine: function(long1, lat1, long2, lat2) {
			var points = new Array(
				new OpenLayers.Geometry.Point(long1, lat1),
				new OpenLayers.Geometry.Point(long2, lat2)
			);
			points.forEach(function(p) {
				p.transform(wgs84, map.getProjectionObject());
			});
			var line = new OpenLayers.Geometry.LineString(points);

			var style = { strokeColor: '#000000', 
				strokeOpacity: 0.5,
				strokeWidth: 5
			};

			var lineFeature = new OpenLayers.Feature.Vector(line, null, style);
			lineLayer.addFeatures([lineFeature]);
		}
	}
	return obj;
})();

function loadMapData(d) {
	// This function is from Google's polyline utility.
	var decodeLine = function (encoded) {
	  var len = encoded.length;
	  var index = 0;
	  var array = [];
	  var lat = 0;
	  var lng = 0;

	  while (index < len) {
		var b;
		var shift = 0;
		var result = 0;
		do {
		  b = encoded.charCodeAt(index++) - 63;
		  result |= (b & 0x1f) << shift;
		  shift += 5;
		} while (b >= 0x20);
		var dlat = ((result & 1) ? ~(result >> 1) : (result >> 1));
		lat += dlat;

		shift = 0;
		result = 0;
		do {
		  b = encoded.charCodeAt(index++) - 63;
		  result |= (b & 0x1f) << shift;
		  shift += 5;
		} while (b >= 0x20);
		var dlng = ((result & 1) ? ~(result >> 1) : (result >> 1));
		lng += dlng;

		array.push([lat * 1e-5, lng * 1e-5]);
	  }

	  return array;
	}

	RouteMap.onInit = function() {
		
		
		
		var mapPoints = [];
		var map = RouteMap.getMap();
		var lineLayer = RouteMap.getLineLayer();
		var proj = RouteMap.getProj();
		var i, point;
		
		if (d.poi) {
			d.poi.forEach(function(poi) {
				var url;
				switch (poi[0]) {
					case "Train":
						url = "http://data.southampton.ac.uk/map-icons/Transportation/train.png";
						break;
					case "Bus":
						url = "http://data.southampton.ac.uk/map-icons/Transportation/bus.png";
						break;
					case "Airport":
						url = "http://data.southampton.ac.uk/map-icons/Transportation/airport.png";
						break;
					default:
						url = "http://data.southampton.ac.uk/map-icons/Transportation/blank.png";
						break;
				}
				RouteMap.addMarker(poi[3], poi[2], url);
			});
		}
		
		if (d.directions) {
			if (d.directions[-1]) {
				//document.getElementById("directions").innerHTML = "<li>Google map directions error: "+d.directions[-1].status+"</li>";
				return;
			}
		
			//Push the starting point
			mapPoints.push(new OpenLayers.Geometry.Point(d.directions[0].lng, d.directions[0].lat));
		
		
			//Go through every point
			for (i = 3; i < d.directions.length - 1; i++) {
				point = d.directions[i];
			
	//			console.log(point);
				//If the point has polygon data, draw it
				if (point.points) {
					decodeLine(point.points).forEach(function(p) {
						mapPoints.push(new OpenLayers.Geometry.Point(p[1], p[0]));
					});
				}
				//Otherwise just connect to the last point
				else {
					//mapPoints.push(new OpenLayers.Geometry.Point(point.lng, point.lat));
				}
			}
		
			//Transform all of the points to the correct projection
			mapPoints.forEach(function(p) {
				p.transform(proj, map.getProjectionObject());
			});
		
			//Turn our list of points in to a line
			var line = new OpenLayers.Geometry.LineString(mapPoints);
			var style = { strokeColor: '#000000', 
				strokeOpacity: 0.5,
				strokeWidth: 5
			};
			var lineFeature = new OpenLayers.Feature.Vector(line, null, style);
			lineLayer.addFeatures([lineFeature]);
		}
	}
}
