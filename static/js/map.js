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
	var icon_size = new OpenLayers.Size(32,37);
	var icon_offset = new OpenLayers.Pixel(-(icon_size.w/2), -icon_size.h);

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

	function startGeolocation() {
		if (navigator.geolocation) {
			var marker;
			
        		navigator.geolocation.watchPosition(function(position) {
        			console.log(position);
        			// Get new Geolocation position
        			if (!marker) {
        				var icon = new OpenLayers.Icon('http://data.southampton.ac.uk/map-icons/Media/downloadicon.png', icon_size, icon_offset);
        				var p = new OpenLayers.LonLat(position.coords.longitude, position.coords.latitude);
        				p.transform(wgs84, map.getProjectionObject());
					marker = new OpenLayers.Marker(p, icon)
        				markers.addMarker(marker);
        			}
        			else {
        				var px = map.getLayerPxFromViewPortPx(map.getPixelFromLonLat(new OpenLayers.LonLat(position.coords.longitude, position.coords.latitude).transform(wgs84, map.getProjectionObject())));
        				marker.moveTo(px);
        			}
        			
        		}, function(error){
        			// Error getting Geolocation information
        			console.log(error);
        		});
        	}
	}

	function init(long1, lat1, long2, lat2) {
		var left = Math.min(long1, long2);
		var top = Math.min(lat1, lat2);
		var right = Math.max(long1, long2);
		var bottom = Math.max(lat1, lat2);
		
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
			restrictedExtent: restrictedExtent,
			controls: []
		};
		map = new OpenLayers.Map('map', options);

		var streetview = new OpenLayers.Layer.StreetView("OS StreetView (1:10000)");

		markers = new OpenLayers.Layer.Markers("Editable Markers");

		map.addLayers([streetview, markers]);
			

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

		var pzb = new OpenLayers.Control.PanZoom({'position': new OpenLayers.Pixel(100,0) });
		pzb.position = new OpenLayers.Pixel(5,30);
		map.addControl(pzb);

		map.addControl(new OpenLayers.Control.Navigation());
		map.addControl(new OpenLayers.Control.Attribution());

	        obj.onInit();  
        	
        	startGeolocation();
        	
		var icon1 = new OpenLayers.Icon('http://data.southampton.ac.uk/map-icons/Restaurants-and-Hotels/hotel_0star.png', icon_size, icon_offset);
		var icon2 = new OpenLayers.Icon('http://data.southampton.ac.uk/map-icons/Restaurants-and-Hotels/hostel_0star.png', icon_size, icon_offset);
		var p1 = new OpenLayers.LonLat(long1, lat1);
		var p2 = new OpenLayers.LonLat(long2, lat2);
		p1.transform(wgs84, map.getProjectionObject());
		p2.transform(wgs84, map.getProjectionObject());
		
		var m1 = new OpenLayers.Marker(p1, icon1);
		addEventToMarker(m1);
		markers.addMarker(m1);
		var m2 = new OpenLayers.Marker(p2, icon2);
		addEventToMarker(m2);
		markers.addMarker(m2);
		
		
		
	}
	
	function addEventToMarker(marker)
	{
		marker.id = "1";
		marker.events.register("mousedown", marker, function() {
			alert(this.id);
		});
		//alert("adding marker");
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
			var icon = new OpenLayers.Icon(img, icon_size, icon_offset);
			var p1 = new OpenLayers.LonLat(long1, lat1);
			p1.transform(wgs84, map.getProjectionObject());
			var marker = new OpenLayers.Marker(p1, icon);
			markers.addMarker(marker);
		},
		init: init,
		onInit: function() {},
		/* Add a route so that it can be drawn with drawRoute() */
		addRoute: function(route) {
			var item = $("<li class='route-name'>"+route.fromname+" to "+route.toname+"</li>");
			$('#directions-list').append(item);
			item.click(function() {
				RouteMap.showRoute(route);
				item.addClass("selected");
			});
		},
		showRoute: function(route) {
			lineLayer.removeAllFeatures();
			$('.route-name').removeClass("selected");
			//Draw driving line
			var line = new OpenLayers.Geometry.LineString(route.driving);
			var style = { strokeColor: '#000000', 
				strokeOpacity: 0.5,
				strokeWidth: 5
			};
			var lineFeature = new OpenLayers.Feature.Vector(line, null, style);
			lineLayer.addFeatures([lineFeature]);

			
			//Draw walking line
			var line2 = new OpenLayers.Geometry.LineString(route.walking);
			var style2 = { strokeColor: '#008', 
				strokeOpacity: 0.5,
				strokeWidth: 5
			};
			var lineFeature2 = new OpenLayers.Feature.Vector(line2, null, style2);
			lineLayer.addFeatures([lineFeature2]);
		},
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

$(function() {
	//Set up Show Routes toggle button
	$('#directions').hide();
	$('#show-routes').click(function() {
		$('#directions').slideToggle();
	});
});

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
		
		var map = RouteMap.getMap();
		var lineLayer = RouteMap.getLineLayer();
		var proj = RouteMap.getProj();
		var i, point;
		
		if (d.poi) {
			d.poi.forEach(function(poi) {
				var url;
				switch (poi[4]) {
					case "train":
						url = "http://data.southampton.ac.uk/map-icons/Transportation/train.png";
						break;
					case "bus":
						url = "http://data.southampton.ac.uk/map-icons/Transportation/bus.png";
						break;
					case "airport":
						url = "http://data.southampton.ac.uk/map-icons/Transportation/airport.png";
						break;
					default:
						url = "http://data.southampton.ac.uk/map-icons/Transportation/blank.png";
						break;
				}
				RouteMap.addMarker(poi[3], poi[2], url);
			});
		}
		
		if(d.routes) {
			d.routes.forEach(function(route) {
				var d = function(type) {
					var mapPoints = [];
					//Go through every point
					for (i = 0; i < route[type].length; i++) {
						mapPoints.push(new OpenLayers.Geometry.Point(route[type][i][0], route[type][i][1]));
					}
					
					//Transform all of the points to the correct projection
					mapPoints.forEach(function(p) {
						p.transform(proj, map.getProjectionObject());
					});
					route[type] = mapPoints;				
				};
				d('walking');
				d('driving');
				RouteMap.addRoute(route);
		
			});
		}
	}
}
