<?php

class Tiles {
	private static function getXY($lat, $lon, $zoom)
	{
		$xtile = floor((($lon + 180) / 360) * pow(2, $zoom));
		$ytile = floor((1 - log(tan(deg2rad($lat)) + 1 / cos(deg2rad($lat))) / pi()) /2 * pow(2, $zoom));
		return array('x' => $xtile, 'y' => $ytile);
	}

	public static function getList($lat1, $lon1, $lat2, $lon2)
	{
		$arr = array();
		
		$prefix="http://opendatamap.ecs.soton.ac.uk/dev/colin/appathon/tile";
		for($z = 6; $z <= 17; $z++)
		{
			$startXY=(Tiles::getXY($lat1, $lon1, $z));
			$endXY=(Tiles::getXY($lat2, $lon2, $z));

			for($x = $startXY['x']; $x <= $endXY['x']; $x++)
			{
				for($y = $startXY['y']; $y <= $endXY['y']; $y++)
				{
					$arr[] = $prefix.'/'.$z.'/'.$x.'/'.$y.".png";
				}
			}
		}
		
		return $arr;
	}
}

?>
