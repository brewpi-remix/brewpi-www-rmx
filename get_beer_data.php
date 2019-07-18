<?php
/*
 Copyright (C) 2018, 2019 Lee C. Bussy (@LBussy)

 This file is part of LBussy's BrewPi Tools Remix (BrewPi-Tools-RMX).

 BrewPi Script RMX is free software: you can redistribute it and/or
 modify it under the terms of the GNU General Public License as
 published by the Free Software Foundation, either version 3 of the
 License, or (at your option) any later version.

 BrewPi Script RMX is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with BrewPi Script RMX. If not, see <https://www.gnu.org/licenses/>.

 These scripts were originally a part of brewpi-script, an installer for
 the BrewPi project. Legacy support (for the very popular Arduino
 controller) seems to have been discontinued in favor of new hardware.

 All credit for the original brewpi-script goes to @elcojacobs,
 @m-mcgowan, @rbrady, @steersbob, @glibersat, @Niels-R and I'm sure
 many more contributors around the world. My apologies if I have
 missed anyone; those were the names listed as contributors on the
 Legacy branch.

 See: 'original-license.md' for notes about the original project's
 license and credits.
 */

$beerName = $_POST["beername"];
$fileNames = array();
	$currentBeerDir = 'data/' . $beerName;
    if(!file_exists($currentBeerDir)){
        echo json_encode( array( "error" => "directory: $beerName, does not exist" ) );
        return;
    }
	$handle = opendir($currentBeerDir);
	if($handle == false){
	echo json_encode( array( "error" => "Cannot retrieve beer files directory: " . $currentBeerDir ) );
	return;
	}
	$first = true;
	$i=0;
	while (false !== ($file = readdir($handle))){  // Iterate over all json files in directory
	$extension = strtolower(substr(strrchr($file, '.'), 1));
	if($extension == 'json' ){
	  	$jsonFile =  $currentBeerDir . '/' . $file;
		$fileNames[$i] = str_replace(".json", "", $jsonFile); // Strip extension for sorting
		$i=$i+1;
	}
}
closedir($handle);

$cols = "";

if ( !empty($fileNames) ) {

	sort($fileNames, SORT_NATURAL); // Sort files to return them in order from oldest to newest
	array_walk($fileNames, function(&$value) { $value .= '.json'; }); // Add .json again

	// Aggregate all json data for the beer
	$renderedRow = false;
	echo "{\"rows\":[";
	foreach ( $fileNames as $fileName ) {
		$contents = file_get_contents(dirname(__FILE__) . '/' . $fileName);
		if ( strlen($contents) != 0 ) {
			if ( $renderedRow ) {
				echo ","; // Comma between each file's rows array
			}
			echo get_list_between($contents, '"rows":' , ']}]');
			$renderedRow = true;

			$colsThisFile = get_list_between($contents, '"cols":' , ']');
			if(strlen($colsThisFile) > strlen($cols)){
			    // Use largest column list
			    $cols = $colsThisFile;
			}
		}
	}
    echo '],"cols":[' . $cols . ']}';
}

function get_list_between($string, $start, $end){
    $begin = strpos($string,$start);
    if ($begin == 0) return "[]"; // return empty list when not found
    $begin = strpos($string,"[", $begin) + 1; // start after list opening bracket
    $len = strpos($string,$end,$begin) - $begin + strlen($end) - 1;
    return substr($string,$begin,$len);
}
