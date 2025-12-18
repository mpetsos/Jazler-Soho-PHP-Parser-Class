<?php
//include class
include('../src/jazler_class.php');

//get data
$timezone = 'UTC'; //your server timezone
$jClass = new jazlerClass();
// Example output
$data = $jClass->jazlerMerge("https://yourwebsite.com/NowOnAir.xml","https://yourwebsite.com/AirPlayHistory.xml","https://yourwebsite.com/AirPlayNext.xml",$timezone);

//create json
header('Content-Type: application/json');
echo json_encode($data, JSON_UNESCAPED_UNICODE);