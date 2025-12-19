<?php
/**
 * Jazler Soho PHP Class Parser V1.0
 * Author: Webat.gr
 * Description: Parses Jazler SOHO XML data files and merges them into a unified array.
 * Requirements: PHP 8+, cURL, SimpleXML
 */
class jazlerClass {
	
	/**
	/** CURL function
	*/
	public function parseCurl($url){
		// Initialize cURL
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // follow redirects
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // optional, skip SSL verification if needed

		$response = curl_exec($ch);

		if (curl_errno($ch)) {
			echo "cURL error: " . curl_error($ch);
			curl_close($ch);
			exit;
		}

		curl_close($ch);
		
		return $response;
	}
	
	/**
	/** CONVERT LOCAL TIME TO SERVER TIME function
	*/
	public function convertToLocalServerTime($timeStr,$localTZ) {
		$serverTZ = date_default_timezone_get();
		$date = new DateTime($timeStr, new DateTimeZone($localTZ));
		$date->setTimezone(new DateTimeZone($serverTZ));
		
		return $date->format('H:i:s');
	}	
	
	/**
	/** PLAYING NOW function
	*/
	public function jazlerNowPlaying($url,$timezone){
		//get data 
		$response = $this->parseCurl($url);
		// Parse the XML with simplexml
		$xml = simplexml_load_string($response);

		if ($xml === false) {
			return 'Error';
		}

		//create array
		$data = json_decode(json_encode($xml),true);

		//get values
		$values = [];
		$values['now_playing']['title'] = $data['Event']['Song']['@attributes']['title'] ?? '';//song title
		$values['now_playing']['artist'] = $data['Event']['Song']['Artist']['@attributes']['name'] ?? '';//artist name
		$values['now_playing']['start_time'] = $this->convertToLocalServerTime($data['Event']['@attributes']['startTime'],$timezone) ?? '';//start time
		$values['now_playing']['end_time'] = $this->convertToLocalServerTime($data['Event']['Song']['Expire']['@attributes']['Time'],$timezone) ?? '';//end time
		$values['now_playing']['duration'] = $data['Event']['Song']['Media']['@attributes']['runTime'] ?? '';//song duration
		$values['now_playing']['kind'] = $data['Event']['@attributes']['eventType'] ?? '';//playing now kind (song, spot etc)
		$values['now_playing']['time_remain'] = strtotime($this->convertToLocalServerTime($data['Event']['Song']['Expire']['@attributes']['Time'],$timezone)) - time();//remain time
		
		return $values;
	}
	
	/**
	/** HISTORY PLAY function
	*/
	public function jazlerHistoryPlay($url,$timezone){		
		//get data 
		$response = $this->parseCurl($url);
		// Parse the XML with simplexml
		$xml = simplexml_load_string($response);

		if ($xml === false) {
			return 'Error';
		}
		
		//create array
		$data = json_decode(json_encode($xml),true);
		
		//get data and create array
		$values = [];
		$data['Song'] = array_reverse($data['Song']);
		foreach($data['Song'] as $key=>$value){
			$values['history'][$key]['title'] = $value['@attributes']['title'] ?? ''; //song title
			$values['history'][$key]['artist'] = $value['Artist']['@attributes']['name'] ?? ''; //artist name
			$values['history'][$key]['start_time'] = $this->convertToLocalServerTime($value['Info']['@attributes']['StartTime'],$timezone) ?? ''; //time played
			$values['history'][$key]['duration'] = $value['Media']['@attributes']['runTime'] ?? ''; //song duration
		}
			
		return $values;
	}
	
	/**
	/** NEXT SONGS PLAY function
	*/
	public function jazlerNextPlay($url,$timezone){		
		//get data 
		$response = $this->parseCurl($url);
		// Parse the XML with simplexml
		$xml = simplexml_load_string($response);

		if ($xml === false) {
			return 'Error';
		}
		
		//create array
		$data = json_decode(json_encode($xml),true);
		
		//get data and create array
		$values = [];
		foreach($data['Song'] as $key=>$value){
			$values['next'][$key]['title'] = $value['@attributes']['title'] ?? ''; //song title
			$values['next'][$key]['artist'] = $value['Artist']['@attributes']['name'] ?? ''; //artist name
			$values['next'][$key]['start_time'] = $this->convertToLocalServerTime($value['Info']['@attributes']['StartTime'],$timezone) ?? ''; //time played
			$values['next'][$key]['duration'] = $value['Media']['@attributes']['runTime'] ?? ''; //song duration
		}
			
		return $values;
	}
	
	/**
	/** MERGE ALL DATA IN ONE ARRAY function
	*/
	public function jazlerMerge($playNowUrl,$playHistoryUrl,$playNextUrl,$timezone){		
		//get all data functions
		$palyNow = $this->jazlerNowPlaying($playNowUrl,$timezone);//play now data
		$playHistory = $this->jazlerHistoryPlay($playHistoryUrl,$timezone);//play history data
		$nextHistory = $this->jazlerNextPlay($playNextUrl,$timezone);//play next data
		
		$results = array_merge($palyNow, $playHistory, $nextHistory);
		
		return $results;
	
	}
	
}


