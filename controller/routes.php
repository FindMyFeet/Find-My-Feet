<?php

require_once("model/location.php");
require_once("model/geo-lookup.php");


class RoutesController extends Controller {
	public $template = "routes";
	public $title = "Routes";
	public $serve_json = true;
	
	public function init($get) {
		if (!isset($get['from']) || !$get['from'] || !isset($get['to']) || !$get['to']) {
			$this->error("Missing parameters from/to", 400);
		}
		//Test the location thingy!
		$this->data['from']  = GeoLookUp::getPostcodeData(strtoupper($get['from']));
		$this->data['to'] = GeoLookUp::getPostcodeData(strtoupper($get['to']));
		$this->lat1 = $this->data['from']['lat'];
		$this->lat2 = $this->data['to']['lat'];
		$this->long1 = $this->data['from']['long'];
		$this->long2 = $this->data['to']['long'];		

		if (!$this->lat1 || !$this->lat2) {
			$this->error("Invalid postcodes", 400);
		}

		$this->longlats = "lat1={$this->lat1}&long1={$this->long1}&lat2={$this->lat2}&long2={$this->long2}";
		$this->manifest = "manifest.php?".$this->longlats;

		$this->jscripts[] = "static/js/OpenLayers-2.11/OpenLayers.js";
		$this->jscripts[] = "static/js/OSecs.js";
		$this->jscripts[] = "static/js/map.js";		
		$this->jscripts[] = "index.php?page=route&".$this->longlats."&jsonp=loadMapData"; //cachable JSONP data
		
	}

}



?>
