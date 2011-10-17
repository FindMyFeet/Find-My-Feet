<?php 
	require_once("model/location.php");
	require_once("model/geo-lookup.php");
	header("content-type: text/cache-manifest");
?>
CACHE MANIFEST
# Map markers
http://data.southampton.ac.uk/map-icons/Restaurants-and-Hotels/hotel_0star.png
http://data.southampton.ac.uk/map-icons/Restaurants-and-Hotels/hostel_0star.png
http://data.southampton.ac.uk/map-icons/Transportation/train.png
http://data.southampton.ac.uk/map-icons/Transportation/bus.png
http://data.southampton.ac.uk/map-icons/Transportation/airport.png
http://data.southampton.ac.uk/map-icons/Transportation/blank.png
<?php

	$files = array();
	$files[] = "static/css/styles.css";
	//$files[] = "static/js";
	$files[] = "static/js/global.js";
	$files[] = "static/js/OpenLayers-2.11/OpenLayers.js";
	$files[] = "static/js/OSecs.js";
	$files[] = "static/js/map.js";
	//$files[] = "static/js/OpenLayers-2.11/theme/default/style.css";
	$files[] = "static/js/OpenLayers-2.11/img/blank.gif";
	$files[] = "static/js/OpenLayers-2.11/img/north-mini.png";
	$files[] = "static/js/OpenLayers-2.11/img/west-mini.png";
	$files[] = "static/js/OpenLayers-2.11/img/east-mini.png";
	$files[] = "static/js/OpenLayers-2.11/img/south-mini.png";
	$files[] = "static/js/OpenLayers-2.11/img/zoom-plus-mini.png";
	$files[] = "static/js/OpenLayers-2.11/img/zoom-world-mini.png";
	$files[] = "static/js/OpenLayers-2.11/img/zoom-minus-mini.png";

	$files[] = "static/js/OpenLayers-2.11/theme/default/framedCloud.css";
	$files[] = "static/js/OpenLayers-2.11/theme/default/google.css";
	$files[] = "static/js/OpenLayers-2.11/theme/default/google.tidy.css";
	$files[] = "static/js/OpenLayers-2.11/theme/default/ie6-style.css";
	$files[] = "static/js/OpenLayers-2.11/theme/default/ie6-style.tidy.css";
	$files[] = "static/js/OpenLayers-2.11/theme/default/img/add_point_off.png";
	$files[] = "static/js/OpenLayers-2.11/theme/default/img/add_point_on.png";
	$files[] = "static/js/OpenLayers-2.11/theme/default/img/blank.gif";
	$files[] = "static/js/OpenLayers-2.11/theme/default/img/close.gif";
	$files[] = "static/js/OpenLayers-2.11/theme/default/img/drag-rectangle-off.png";
	$files[] = "static/js/OpenLayers-2.11/theme/default/img/drag-rectangle-on.png";
	$files[] = "static/js/OpenLayers-2.11/theme/default/img/draw_line_off.png";
	$files[] = "static/js/OpenLayers-2.11/theme/default/img/draw_line_on.png";
	$files[] = "static/js/OpenLayers-2.11/theme/default/img/draw_point_off.png";
	$files[] = "static/js/OpenLayers-2.11/theme/default/img/draw_point_on.png";
	$files[] = "static/js/OpenLayers-2.11/theme/default/img/draw_polygon_off.png";
	$files[] = "static/js/OpenLayers-2.11/theme/default/img/draw_polygon_on.png";
	$files[] = "static/js/OpenLayers-2.11/theme/default/img/editing_tool_bar.png";
	$files[] = "static/js/OpenLayers-2.11/theme/default/img/move_feature_off.png";
	$files[] = "static/js/OpenLayers-2.11/theme/default/img/move_feature_on.png";
	$files[] = "static/js/OpenLayers-2.11/theme/default/img/navigation_history.png";
	$files[] = "static/js/OpenLayers-2.11/theme/default/img/overview_replacement.gif";
	$files[] = "static/js/OpenLayers-2.11/theme/default/img/pan-panel-NOALPHA.png";
	$files[] = "static/js/OpenLayers-2.11/theme/default/img/pan-panel.png";
	$files[] = "static/js/OpenLayers-2.11/theme/default/img/pan_off.png";
	$files[] = "static/js/OpenLayers-2.11/theme/default/img/pan_on.png";
	$files[] = "static/js/OpenLayers-2.11/theme/default/img/panning-hand-off.png";
	$files[] = "static/js/OpenLayers-2.11/theme/default/img/panning-hand-on.png";
	$files[] = "static/js/OpenLayers-2.11/theme/default/img/remove_point_off.png";
	$files[] = "static/js/OpenLayers-2.11/theme/default/img/remove_point_on.png";
	$files[] = "static/js/OpenLayers-2.11/theme/default/img/ruler.png";
	$files[] = "static/js/OpenLayers-2.11/theme/default/img/save_features_off.png";
	$files[] = "static/js/OpenLayers-2.11/theme/default/img/save_features_on.png";
	$files[] = "static/js/OpenLayers-2.11/theme/default/img/view_next_off.png";
	$files[] = "static/js/OpenLayers-2.11/theme/default/img/view_next_on.png";
	$files[] = "static/js/OpenLayers-2.11/theme/default/img/view_previous_off.png";
	$files[] = "static/js/OpenLayers-2.11/theme/default/img/view_previous_on.png";
	$files[] = "static/js/OpenLayers-2.11/theme/default/img/zoom-panel-NOALPHA.png";
	$files[] = "static/js/OpenLayers-2.11/theme/default/img/zoom-panel.png";
	$files[] = "static/js/OpenLayers-2.11/theme/default/style.css";
	$files[] = "static/js/OpenLayers-2.11/theme/default/style.tidy.css";


	$args = filter_input_array(INPUT_GET, array(
		'lat1' => FILTER_VALIDATE_FLOAT,
		'long1' => FILTER_VALIDATE_FLOAT,
		'lat2' => FILTER_VALIDATE_FLOAT,
		'long2' => FILTER_VALIDATE_FLOAT
	));

	
	//Here we cache the list of map images.
	if (!in_array(NULL, $args, true)) {
		$longlats = "lat1={$args['lat1']}&long1={$args['long1']}&lat2={$args['lat2']}&long2={$args['long2']}";
		$t = Tiles::getList($args['lat1'], $args['long1'], $args['lat2'], $args['long2']);
		$files = array_merge($files, $t);
		//This JSONP file contains all the path and marker data and needs to be cached.
		$files[] = "index.php?page=route&".$longlats."&jsonp=loadMapData";		
	}
	
	//Output the contents of $files[], add domain prefix where appropriate.
	foreach ($files as $file) {
		if(substr($file, 0, 4) != 'http')
			echo "http://ec2-50-16-75-143.compute-1.amazonaws.com/".$file."\n";
		else
			echo $file."\n";
	}
	
?>

NETWORK:
*
