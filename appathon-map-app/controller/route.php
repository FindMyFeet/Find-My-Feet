<?php

require_once("model/location.php");
require_once("model/geo-lookup.php");
require_once("model/nearstation.php");

class routeController extends Controller {
	public $template = "route";
	public $title = "Route";
	public $serve_json = true;
	
	public function init($get) {
		if (isset($_GET['fake_data1'])) {
			
		}
		
		else {
		
			$args = filter_input_array(INPUT_GET, array(
				'lat1' => FILTER_VALIDATE_FLOAT,
				'long1' => FILTER_VALIDATE_FLOAT,
				'lat2' => FILTER_VALIDATE_FLOAT,
				'long2' => FILTER_VALIDATE_FLOAT
			));
			if (in_array(NULL, $args, true)) {
				$this->error("Missing parameters lat1/lat2/long1/long2", 400);
			}
			//Test the location thingy!
		
			$this->data = array(
				"images" => Tiles::getList($args['lat1'], $args['long1'], $args['lat2'], $args['long2']),
				"directions" => GeoLookUp::GetWalkingDirections($args['lat1'], $args['long1'], $args['lat2'], $args['long2'], "driving"),
				"poi" => nearAll($args['lat1'], $args['long1'] , $args['lat2'], $args['long2'])
			);
		}
	}

}



?>

