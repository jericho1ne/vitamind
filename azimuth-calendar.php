<?php

date_default_timezone_set("America/Los_Angeles");


// Set defaults
$interval = 20;
$month = date("n");
$day = date("j");
$year = date("Y");


$url = "http://aa.usno.navy.mil/cgi-bin/aa_altazw.pl?form=1&body=10" 
	. "&year={$year}&month={$month}&day={$day}"
	. "&intv_mag={$interval}"
	. "&state=CA&place=santa+monica";


$content = file_get_contents($url);

// Set search boundaries
$searchStart = "o           o"; // <pre>
$searchEnd = "</pre>"; 


// Do the math
$first = strpos($content, $searchStart);	   
$last = strpos($content, $searchEnd);
$length = (strlen($content) - $first) - (strlen($content) - $last) - strlen($searchEnd);

$strippedDown = substr($content, $first + strlen($searchStart), $length - strlen($searchEnd) - 1 );

$csvArray = explode("\n", trim($strippedDown));

// Create array to be printed out as JSON
$azimuth = [];

foreach ($csvArray as $val) {
	$parts = preg_split('/\s+/', $val);
	$alt = $parts[1];
	
		
	if ($alt > 80) 		$color = "#0af4f7";
	else if ($alt > 70)	$color = "#25e10b";
	else if ($alt > 60)	$color = "#7fda09";
	else if ($alt > 50)	$color = "#b0da09";
	else if ($alt > 40)	$color = "#fdc609";
	else if ($alt > 40)	$color = "#cd9b9b";
	else if ($alt > 30)	$color = "#d3b6b6";
	else					$color = "#cbcbcb";  // $alt <= 20
	
	$azimuth[] = [
		'time' => $parts[0],
		'altitude' => $alt,
		'color'	=> $color,
		'azimuth' => $parts[2],
	];
}


//print_r($azimuth);

echo json_encode($azimuth);

?>