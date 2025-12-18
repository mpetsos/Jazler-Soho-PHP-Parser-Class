<?php
//include class
include('../src/jazler_class_api.php');

//get data
$timezone = 'UTC'; //your server timezone
$jClass = new jazlerClassWithAPI();
// Example output
$data = $jClass->jazlerMerge("https://yourwebsite.com/NowOnAir.xml","https://yourwebsite.com/AirPlayHistory.xml","https://yourwebsite.com/AirPlayNext.xml",$timezone,'yourwebsite.com');

//create json
header('Content-Type: application/json');
echo json_encode($data, JSON_UNESCAPED_UNICODE);