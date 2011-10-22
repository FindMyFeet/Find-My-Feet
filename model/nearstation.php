<?php
require_once('../mysql-dbo.inc.php');

$db = getDB();

function nearAll($lat1, $long1, $lat2, $long2)
{
	return array_merge(nearPoint($lat1, $long1), nearPoint($lat2, $long2));
}
function nearPoint($lat, $long)
{
	return array_merge(nearBusStop($lat,$long), nearTrainStation($lat,$long), nearAirport($lat,$long));
}
function nearBusStop($lat, $long)
{
	return nearestTransport("bus",$lat,$long,3);
}

function nearTrainStation($lat, $long)
{
	return nearestTransport("trainstation",$lat,$long,10);
}

function nearAirport($lat, $long)
{
	return nearestTransport("airport",$lat,$long,5);
}

function nearestTransport($type,$latitude,$longitude,$count=1)
{
	$arrResult = array();
	if($type != "bus" && $type != "trainstation" && $type != "airport")
		return;
	$latitude = (float)$latitude;
	$longitude = (float)$longitude;
	$count = (int)$count;
	$q = $db->prepare("
		SELECT * FROM transport WHERE type = :type
		ORDER BY (((acos(sin((:latitude*pi()/180)) * sin((`Latitude`*pi()/180))+cos((:latitude*pi()/180)) * cos((`Latitude`*pi()/180)) * cos(((:longitude - `Longitude`)*pi()/180))))*180/pi())) ASC 
		LIMIT :count"
	);
	$q->bindValue(":type", $type, PDO::PARAM_STR);
	$q->bindValue(":latitude", $latitude);
	$q->bindValue(":longitude", $longitude);
	$result = $q->execute();
	
	while($row = $q->fetch(PDO::FETCH_ASSOC))
	{
		$row['type'] = $type;
		$arrResult[] = $row;
	}
	return $arrResult;
}

?>
