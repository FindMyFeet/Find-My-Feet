<?php

class Tiles {
	private static function getXY($lat, $lon, $zoom)
	{
		$xtile = floor((($lon + 180) / 360) * pow(2, $zoom));
		$ytile = floor((1 - log(tan(deg2rad($lat)) + 1 / cos(deg2rad($lat))) / pi()) /2 * pow(2, $zoom));
		return array('x' => $xtile, 'y' => $ytile);
	}

	public static function getList($points)
	{
		$arr = array();
		$top = -90;
		$bottom = 90;
		$left = 180;
		$right = -180;
		foreach($points as $p)
		{
			$top = max($top, $p[0]);
			$bottom = min($bottom, $p[0]);
			$left = min($left, $p[1]);
			$right = max($right, $p[1]);
		}
		$top += 0.1;
		$bottom -= 0.1;
		$left -= 0.1;
		$right += 0.1;
		
		$prefix="http://opendatamap.ecs.soton.ac.uk/dev/colin/appathon/tile";
		//not 17
		for($z = 6; $z <= 12; $z++)
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

		foreach($points as $p)
		{
			list($top, $left) = getDerivedPos($p, 1.75 * pi());
			list($bottom, $right) = getDerivedPos($p, 0.75 * pi());
			for($z = 13; $z <= 17; $z++)
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
		}

		$arr = array_unique($arr);
		$arr[] = '# Number of map tiles to cache: '.count($arr);		

		return $arr;
	}
}

function getDerivedPos($p, $bearing, $distance = 1)
{
	$latA = deg2rad($p[0]);
	$lonA = deg2rad($p[1]);
	$angDis = $distance / 6378.137;

	$lat = asin(sin($latA) * cos($angDis) + cos($latA) * sin($angDis) * cos($bearing));
	$dlon = atan2(sin($bearing) * sin($angDis) * cos($latA), cos($angDis) - sin($latA) * sin($latA));
	$lon = (($lonA + $dlon + pi()) % 2*pi()) - pi();

	return array(rad2deg($lat), rad2deg($lon));
}

?>
