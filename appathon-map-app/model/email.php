<html>
<head>
	<title>Processing E-mails</title>
	<style type="text/css">
		.highlight
		{
			background-color: blue;
			color: white;
			font-size: large;
			padding: 5px;
			margin: 5px;
			border: 2px solid black;
		}
		.highlight a
		{
			color: white;
		}
	</style>
</head>


<?php


	$parseFunctions = array(
								"flybe"		=>	"parseFlyBe",
								"laterooms"	=>	"parseLateRooms",
								"appathon"	=>	"parseAppathon"
							);
							
	$data = array();
	
	$file = $_GET['file'];

//	if($handle = opendir("."))
	{
//		while(($file = readdir($handle)) !== false)
		{
			if(substr($file, -4) == ".mbs")
			{
//				echo "<h2>$file</h2>";
				
				require_once('../email/MimeMailParser.class.php');

				$path = $file;
				$Parser = new MimeMailParser();
				$Parser->setPath($path);
				
				$to = $Parser->getHeader('to');
				$from = $Parser->getHeader('from');
				$subject = $Parser->getHeader('subject');
				$text = $Parser->getMessageBody('text');
				$html = $Parser->getMessageBody('html');
				$attachments = $Parser->getAttachments();
				
				//echo "<p>$html</p>";
				
				foreach($parseFunctions as $key => $func)
				{
					if(stristr($from, $key) || stristr($subject, $key))
					{
						//echo "<p>$func</p>";
						$out = $func($html);
						$data = array_merge($data, $out);
					}
				}
			}
		}
	}

//	HTML::print_r($data);
	
	
	
	function parseFlyBe($body)
	{
		//echo HTML::p($body);
		
		$start = stripos($body, "<h1>Full Booking Details</h1>");
		
		$count = preg_match_all("/<span class=\"ecxdetailsM\">([A-Za-z0-9 ]+) <\/span><span class=\"ecxdetailsS\">([A-Za-z0-9 ]+)<\/span> <span class=\"ecxdetailsB\"><strong>([A-Za-z ]+) to ([A-Za-z ]+) <\/strong><\/span><span class=\"ecxdetailsS\">([012][0-9]:[0-9][0-9])<\/span> <span class=\"ecxdetailsS\">([012][0-9]:[0-9][0-9])<\/span> <br>/", $body, $matches, PREG_OFFSET_CAPTURE, $start);
		
		//echo HTML::p($count);
		if($count > 0)
		{
			//echo HTML::print_r($matches);
			
			$flight['flightArrivalTime'] = $matches[6][0][0];
			$flight['flightArrivalDate'] = $matches[1][0][0];
			$flight['flightArrivalAirport'] = $matches[3][1][0];
			
			$flight['flightDepartureTime'] = $matches[5][0][1];	// time of departing remote place
			$flight['flightDepartureDate'] = $matches[1][0][1];
			$flight['flightDepartureAirport'] = $matches[4][0][0];
			
		}
		
		
		$body = preg_replace("/<span class=\"ecxdetailsM\">([A-Za-z0-9 ]+) <\/span><span class=\"ecxdetailsS\">([A-Za-z0-9 ]+)<\/span> <span class=\"ecxdetailsB\"><strong>([A-Za-z ]+) to ([A-Za-z ]+) <\/strong><\/span><span class=\"ecxdetailsS\">([012][0-9]:[0-9][0-9])<\/span> <span class=\"ecxdetailsS\">([012][0-9]:[0-9][0-9])<\/span> <br>/",
		
		"<span class=\"ecxdetailsM\"><span class='highlight'>$1</span> </span><span class=\"ecxdetailsS\">$2</span> <span class=\"ecxdetailsB\"><strong><span class='highlight'>$3</span> to <span class='highlight'>$4</span> </strong></span><span class=\"ecxdetailsS\"><span class='highlight'>$5</span></span> <span class=\"ecxdetailsS\"><span class='highlight'>$6</span></span> <br>", $body);
		
		echo $body;
		
		return $flight;
	}
	
	
	
	function parseLateRooms($body)
	{
		//echo HTML::p($body);
		// start of hotel details in LateRooms e-mail
		$start =  stripos($body, "Hotel Details:");
		
		$hotel = array();
		
		$count = preg_match("/<br>([^<]*)<br>/", $body, $matches, PREG_OFFSET_CAPTURE, $start);
		if($count > 0)
		{
			$hotel['hotelName'] = $matches[1][0];
		}
		
		$body = preg_replace("/Hotel Details:<\/font><\/strong><br>([^0-9@<]*)<br>/", "Hotel Details:</font></strong><br><span class='highlight'>$1</span><br>", $body);
		
		// http://en.wikipedia.org/wiki/Postcodes_in_the_United_Kingdom#Validation
		$count = preg_match("/([A-Z]{1,2}[0-9R][0-9A-Z]? [0-9][ABD-HJLNP-UW-Z]{2})/", $body, $matches, PREG_OFFSET_CAPTURE, $start);
		if($count > 0)
		{
			$hotel['hotelPostcode'] = $matches[1][0];
		}
		
		$body = preg_replace("/([A-Z]{1,2}[0-9R][0-9A-Z]? [0-9][ABD-HJLNP-UW-Z]{2})/", "<span class='highlight'>$1</span>", $body);
		
		echo "$body";
		
		return $hotel;
	}
	
	function parseAppathon($body)
	{
		$body = preg_replace("/Where: ([^<]*)/", "Where: <span class='highlight'>$1</span>", $body);
		
		$body = preg_replace("/The event will held here: (.*)<br>/", "The event will held here: <span class='highlight'>$1</span>", $body);
		
		echo $body;
		
		return array();
	}
	
?>


</html>
