<?php 

include_once "model/sparqllib.php";
	
       
class GeoLookUp{
	public static $endpoint = "http://api.talis.com/stores/ordnance-survey/services/sparql";
		
	public static function getPostcodeData($postcode){
		$data = sparql_get(self::$endpoint, "
			   SELECT ?p ?lat ?long WHERE {
			   ?p <http://www.w3.org/2000/01/rdf-schema#label> '$postcode' .
			   ?p <http://www.w3.org/2003/01/geo/wgs84_pos#lat> ?lat .
			   ?p <http://www.w3.org/2003/01/geo/wgs84_pos#long> ?long .
		}
			   ");
		if(count($data) == 1)
			   return $data[0];
		else
			   return null;
    }
    

	private static function parseData($data){
		$arr = array();
		$jsonIterator = new RecursiveIteratorIterator(
		new RecursiveArrayIterator(json_decode($data, TRUE)),
			RecursiveIteratorIterator::SELF_FIRST);
		
		$i = -1;
		foreach ($jsonIterator as $key => $val) {
			if(!is_array($val)) {
				if ($key == "lat") {
					$arr[] = array();
					$i ++;
				}
				$arr[$i][$key] = $val;
			}
		}
		return $arr;
	}
    	
	/*Posible modes:
	 * 	walking
	 *	driving
	 *	cycling
	 */
	public static function GetWalkingDirections($lat1, $long1, $lat2, $long2, $mean = "driving" ){
		$finalURL="http://maps.googleapis.com/maps/api/directions/json?origin=".rawurlencode($lat1).",".rawurlencode($long1)."&destination=".rawurlencode($lat1).",".rawurlencode($long2)."&mode=".$mean."&key=ABQIAAAACg0Yi2FlJ60uRHWKH4VdoRTFahs1cYCDhpfJCLGEE_UEiQVsURT5UXAoIW2dv-kDeg0hjLuin66Nog&sensor=false";
		$data = file_get_contents($finalURL);
		return GeoLookUp::parseData($data);
	}

}

?>