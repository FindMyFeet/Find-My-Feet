<?php


function nearAll($lat1, $long1, $lat2, $long2)
{
  return array_merge(nearBusStop($lat1, $long1, $lat2, $long2), nearTrainStation($lat1, $long1, $lat2, $long2), nearAirport($lat1, $long1, $lat2, $long2));

}
function nearBusStop($lat1, $long1, $lat2, $long2)
{
	return nearTransport($lat1, $long1, $lat2, $long2, './model/data/Stops.csv', 'Bus');
}

function nearTrainStation($lat1, $long1, $lat2, $long2)
{
	return nearTransport($lat1, $long1, $lat2, $long2, './model/data/RailReferences.csv', 'Train');
}

function nearAirport($lat1, $long1, $lat2, $long2)
{
	return array();
	//return nearTransport($lat1, $long1, $lat2, $long2, './model/data/AirReferences.csv', 'Airport');
}

function distance($lat1, $lon1, $lat2, $lon2) {
  $theta = $lon1 - $lon2; 
  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)); 
  $dist = acos($dist); 
  return rad2deg($dist);
}

function nearTransport($lat1, $long1, $lat2, $long2, $csvfile, $type)
{
  $arrResult = array();
  $data = array();
  $top = max($lat1, $lat2) + 0.1;
  $bottom = min($lat1, $lat2) - 0.1;
  $left = min($long1, $long2) - 0.1;
  $right = max($long1, $long2) + 0.1;
  $lowestdistance1 = PHP_INT_MAX;
  $lowestdistance2 = PHP_INT_MAX;

  if (($handle = fopen($csvfile, "r")) !== FALSE) {
    while (($location = fgetcsv($handle, 1000, ",")) !== FALSE) {
      //print ($location['6'].$location['7']."\n");

      //echo $location['30'].", ".$location['29']."\n";

      if($type == 'Bus')
        $geo = array($location['30'], $location['29']);
      else if($type == 'Train')
        $geo = convertXToLong($location['6'],$location['7']);

      if($geo[1] >= $left && $geo[1] <= $right && $geo[0] <= $top && $geo[0] >= $bottom)
      { 
	if($type == 'Bus')
	{
	  $distance1 = distance($lat1, $long1, $geo[0], $geo[1]);
	  $distance2 = distance($lat2, $long2, $geo[0], $geo[1]);
	  if($distance1 < $lowestdistance1)
	  {
	    $closest1 = array($type, $location['3'], $geo[0], $geo[1]);
	    $lowestdistance1 = $distance1;
	  }
	  if($distance2 < $lowestdistance2)
	  {
	    $closest2 = array($type, $location['3'], $geo[0], $geo[1]);
	    $lowestdistance2 = $distance2;
	  }
	}
	else
	{
	  $data[] = array($type, $location['3'], $geo[0], $geo[1]);
	}
      }
    }
  }

  if($type == 'Bus')
    return array($closest1, $closest2);
  //print_r($data);
  return $data;
}


function convertXToLong($xCoord, $yCoord)
{
	$E = $xCoord;
	$N = $yCoord;
	$a = 6377563.396;
	$b = 6356256.910;              // Airy 1830 major & minor semi-axes
	$F0 = 0.9996012717;                             // NatGrid scale factor on central meridian
	$lat0 = 49*pi()/180;
	$lon0 = -2*pi()/180;  // NatGrid true origin
	$N0 = -100000;
	$E0 = 400000;                     // northing & easting of true origin, metres

	$e2 = 1 - ($b*$b)/($a*$a);                          // eccentricity squared
	$n = ($a-$b)/($a+$b);
	$n2 = $n*$n;
	$n3 = $n*$n*$n;

	$lat=$lat0;
	$M=0;
	do {
	$lat = ($N-$N0-$M)/($a*$F0) + $lat;

	$Ma = (1 + $n + (5/4)*$n2 + (5/4)*$n3) * ($lat-$lat0);
	$Mb = (3*$n + 3*$n*$n + (21/8)*$n3) * sin($lat-$lat0) * cos($lat+$lat0);
	$Mc = ((15/8)*$n2 + (15/8)*$n3) * sin(2*($lat-$lat0)) * cos(2*($lat+$lat0));
	$Md = (35/24)*$n3 * sin(3*($lat-$lat0)) * cos(3*($lat+$lat0));
	$M = $b * $F0 * ($Ma - $Mb + $Mc - $Md);             // meridional arc

	} while ($N-$N0-$M >= 0.00001);  // ie until < 0.01mm

	$cosLat = cos($lat);
	$sinLat = sin($lat);
	$nu = $a*$F0/sqrt(1-$e2*$sinLat*$sinLat);              // transverse radius of curvature
	$rho = $a*$F0*(1-$e2)/pow(1-$e2*$sinLat*$sinLat, 1.5);  // meridional radius of curvature
	$eta2 = $nu/$rho-1;

	$tanLat = tan($lat);
	$tan2lat = $tanLat*$tanLat;
	$tan4lat = $tan2lat*$tan2lat;
	$tan6lat = $tan4lat*$tan2lat;
	$secLat = 1/$cosLat;
	$nu3 = $nu*$nu*$nu;
	$nu5 = $nu3*$nu*$nu;
	$nu7 = $nu5*$nu*$nu;
	$VII = $tanLat/(2*$rho*$nu);
	$VIII = $tanLat/(24*$rho*$nu3)*(5+3*$tan2lat+$eta2-9*$tan2lat*$eta2);
	$IX = $tanLat/(720*$rho*$nu5)*(61+90*$tan2lat+45*$tan4lat);
	$X = $secLat/$nu;
	$XI = $secLat/(6*$nu3)*($nu/$rho+2*$tan2lat);
	$XII = $secLat/(120*$nu5)*(5+28*$tan2lat+24*$tan4lat);
	$XIIA = $secLat/(5040*$nu7)*(61+662*$tan2lat+1320*$tan4lat+720*$tan6lat);

	$dE = ($E-$E0);
	$dE2 = $dE*$dE;
	$dE3 = $dE2*$dE;
	$dE4 = $dE2*$dE2;
	$dE5 = $dE3*$dE2;
	$dE6 = $dE4*$dE2;
	$dE7 = $dE5*$dE2;
	$lat = $lat - $VII*$dE2 + $VIII*$dE4 - $IX*$dE6;
	$lon = $lon0 + $X*$dE - $XI*$dE3 + $XII*$dE5 - $XIIA*$dE7;

	$lat = $lat*180/pi();
	$lon = $lon*180/pi();
	return array($lat, $lon);
}

//nearBusStop(50.955251, -1.368424,50.943787,-1.355935);
//print_r(nearTrainStation(50.955251, -1.368424,50.943787,-1.355935));
//nearAirport(50.955251, -1.368424,50.943787,-1.355935);

?>

