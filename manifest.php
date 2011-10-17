<?php 
	require_once("model/location.php");
	require_once("model/geo-lookup.php");
	header("content-type: text/cache-manifest");
?>
CACHE MANIFEST

<?php

	$files = array();
	
	$files[] = "index.php";
	$files[] = "static/css/styles.css";
	//$files[] = "static/js";
	$files[] = "static/js/global.js";
	//$files[] = "static/js/OpenLayers-2.11/OpenLayers.js";
	$files[] = "static/js/OSecs.js";
	$files[] = "static/js/map.js";
	//$files[] = "static/js/OpenLayers-2.11/theme/default/style.css";
	$files[] = "static/js/OpenLayers-2.11/img/north-mini.png";
	$files[] = "static/js/OpenLayers-2.11/img/west-mini.png";
	$files[] = "static/js/OpenLayers-2.11/img/east-mini.png";
	$files[] = "static/js/OpenLayers-2.11/img/south-mini.png";
	$files[] = "static/js/OpenLayers-2.11/img/zoom-plus-mini.png";
	$files[] = "static/js/OpenLayers-2.11/img/zoom-world-mini.png";
	$files[] = "static/js/OpenLayers-2.11/img/zoom-minus-mini.png";

	$args = filter_input_array(INPUT_GET, array(
		'lat1' => FILTER_VALIDATE_FLOAT,
		'long1' => FILTER_VALIDATE_FLOAT,
		'lat2' => FILTER_VALIDATE_FLOAT,
		'long2' => FILTER_VALIDATE_FLOAT
	));
	//$longlats = "lat1={$args['lat1']}&long1={$args['long1']}&lat2={$args['lat2']}&long2={$args['long2']}";
	//$files[] = "index.php?page=tiles&".$longlats."&jsonp=loadMapData";
	
	if (!in_array(NULL, $args, true)) {
		$t = Tiles::getList($args['lat1'], $args['long1'], $args['lat2'], $args['long2']);
		$files = array_merge($files, $t);
	}
	
	foreach ($files as $file) {
		if(substr($file, 0, 4) != 'http')
			echo "http://ec2-50-16-75-143.compute-1.amazonaws.com/".$file."\n";
		else
			echo $file."\n";
	}
	
?>

