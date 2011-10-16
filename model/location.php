<?php

class Tiles {
	private static function getXY($lat, $lon, $zoom)
	{
		$xtile = floor((($lon + 180) / 360) * pow(2, $zoom));
		$ytile = floor((1 - log(tan(deg2rad($lat)) + 1 / cos(deg2rad($lat))) / pi()) /2 * pow(2, $zoom));
		return array('x' => $xtile, 'y' => $ytile);
	}

	public static function getList($lat1, $long1, $lat2, $long2)
	{
		$arr = array();
		$top = max($lat1, $lat2) + 0.1;
		$bottom = min($lat1, $lat2) - 0.1;
		$left = min($long1, $long2) - 0.1;
		$right = max($long1, $long2) + 0.1;
		
		$prefix="http://opendatamap.ecs.soton.ac.uk/dev/colin/appathon/tile";
		for($z = 6; $z <= 17; $z++)
		{
			$startXY=(Tiles::getXY($top, $left, $z));
			$endXY=(Tiles::getXY($bottom, $right, $z));

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
