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
			/*
			$args = filter_input_array(INPUT_GET, array(
				'lat1' => FILTER_VALIDATE_FLOAT,
				'long1' => FILTER_VALIDATE_FLOAT,
				'lat2' => FILTER_VALIDATE_FLOAT,
				'long2' => FILTER_VALIDATE_FLOAT
			));
			*/
			$args = $_GET;
			if (in_array(NULL, $args, true)) {
				$this->error("Missing parameters lat1/lat2/long1/long2", 400);
			}
			//Test the location thingy!
			$user_points = array(array($args['lat1'], $args['long1']), array($args['lat1'], $args['long1']));

			$poi_list = array();
			$routes = array();
					
			foreach ($user_points as $p) {
				$busses = nearBusStop($p[0], $p[1]);
				$trains = nearTrainStation($p[0], $p[1]);
				$airports = nearAirport($p[0], $p[1]);
				
				$routes[] = GeoLookUp::GetDirections($airports[0], $p);
				$routes[] = GeoLookUp::GetDirections($airports[0], $p);
				$routes[] = GeoLookUp::GetDirections($p, $trains[0]);
				$routes[] = GeoLookUp::GetDirections($p, $airports[0]);
				$points = array_merge($busses, $trains, $airports);
				
				foreach ($points as $poi) {
					if (!array_key_exists($poi[0], $poi_list)) {
						$poi_list[$poi[0]] = $poi;
					}
				}
				
				foreach ($user_points as $p2) {
					if ($p2[0] === $p[0] && $p2[1] === $p[1]) {
						$routes[] = GeoLookUp::GetDirections($p, $p2);
					}
				}
			}
					
			$this->data = array(
				"images" => Tiles::getList($user_points),
				"routes" => $routes,
				"poi" => $poi_list
			);
		}
	}

}



?>

