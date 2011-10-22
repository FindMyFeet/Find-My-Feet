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
			$user_points = array(array('Hotel', 'Hotel', $args['lat1'], $args['long1']), array('Venue', 'Venue', $args['lat2'], $args['long2']));

			$poi_list = array();
			$routes = array();
					
			foreach ($user_points as $p) {
				foreach ($user_points as $p2) {
					if ($p2[2] != $p[2] || $p2[3] != $p[3]) {
						$routes[] = GeoLookUp::GetDirections($p, $p2);
					}
				}
			}

			foreach ($user_points as $p) {
				$busses = nearBusStop($p[2], $p[3]);
				$trains = nearTrainStation($p[2], $p[3]);
				$airports = nearAirport($p[2], $p[3]);
				
				$routes[] = GeoLookUp::GetDirections($airports[0], $p);
				$routes[] = GeoLookUp::GetDirections($p, $airports[0]);
				$routes[] = GeoLookUp::GetDirections($trains[0], $p);
				$routes[] = GeoLookUp::GetDirections($p, $trains[0]);
				$points = array_merge($busses, $trains, $airports);
				
				foreach ($points as $poi) {
					if (!array_key_exists($poi[0], $poi_list)) {
						$poi_list[$poi[0]] = $poi;
					}
				}
			}
					
			$this->data = array(
				"images" => Tiles::getList($user_points),
				"routes" => $routes,
				"poi" => array_values($poi_list)
			);
		}
	}

}



?>

