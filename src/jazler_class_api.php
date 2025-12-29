<?php
/**
 * Jazler Soho PHP Class Parser V1.0 (with greek-radios.gr API)
 * Author: Webat.gr
 * Description: Parses Jazler SOHO XML data files and merges them into a unified array.
 * Requirements: PHP 8+, cURL, SimpleXML
 */
class jazlerClassWithAPI {
	
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
	public function jazlerNowPlaying($url,$timezone,$apiSite){
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
		
		//start parse API data
		$artist = explode('-',$data['Event']['Song']['Artist']['@attributes']['name']);
		$artist = explode(',',$artist[0]);
		$artistname = $artist[0];
		$apiData = $this->parseCurl('https://www.greek-radios.gr/artist-info/artist-api.php?artist='.urlencode(trim($artistname)).'&song='.urlencode($data['Event']['Song']['@attributes']['title']).'&site='.urlencode($apiSite));
		$apiData = json_decode($apiData,true);
		if(!isset($apiData['error'])){
			$values['now_playing']['artist_bio'] = $apiData['biography'] ?? '';
			$values['now_playing']['artist_image'] = $apiData['image'] ?? '';
			if(isset($apiData['lyrics'])){
				$values['now_playing']['song_lyrics'] = $apiData['lyrics'] ?? '';
			} else {
				$values['now_playing']['song_lyrics'] = '';
			}
			
		} else {
			$values['now_playing']['artist_bio'] = '';
			$values['now_playing']['artist_image'] = '';
			$values['now_playing']['song_lyrics'] = '';
		}
		
		return $values;
	}
	
	/**
	/** HISTORY PLAY function
	*/
	public function jazlerHistoryPlay($url,$timezone,$apiSite){		
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
			//start parse API data
			$artist = explode('-',$value['Artist']['@attributes']['name']);
			$artist = explode(',',$artist[0]);
			$artistname = $artist[0];
			$apiData = $this->parseCurl('https://www.greek-radios.gr/artist-info/artist-api.php?artist='.urlencode(trim($artistname)).'&song='.urlencode($value['@attributes']['title']).'&site='.urlencode($apiSite));
			$apiData = json_decode($apiData,true);
			if(!isset($apiData['error'])){
				$values['history'][$key]['artist_bio'] = $apiData['biography'] ?? '';
				$values['history'][$key]['artist_image'] = $apiData['image'] ?? '';
				if(isset($apiData['lyrics'])){
					$values['history'][$key]['song_lyrics'] = $apiData['lyrics'] ?? '';
				} else {
					$values['history'][$key]['song_lyrics'] = '';
				}

				
			} else {
				$values['history'][$key]['artist_bio'] = '';
				$values['history'][$key]['artist_image'] = '';
				$values['history'][$key]['song_lyrics'] = '';
			}
		
		}
		
		return $values;
	}
	
	/**
	/** NEXT SONGS PLAY function
	*/
	public function jazlerNextPlay($url,$timezone,$apiSite){		
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
			//start parse API data
			$artist = explode('-',$value['Artist']['@attributes']['name']);
			$artist = explode(',',$artist[0]);
			$artistname = $artist[0];
			$apiData = $this->parseCurl('https://www.greek-radios.gr/artist-info/artist-api.php?artist='.urlencode(trim($artistname)).'&song='.urlencode($value['@attributes']['title']).'&site='.urlencode($apiSite));
			$apiData = json_decode($apiData,true);
			if(!isset($apiData['error'])){
				$values['next'][$key]['artist_bio'] = $apiData['biography'] ?? '';
				$values['next'][$key]['artist_image'] = $apiData['image'] ?? '';
				if(isset($apiData['lyrics'])){
					$values['next'][$key]['song_lyrics'] = $apiData['lyrics'] ?? '';
				} else {
					$values['next'][$key]['song_lyrics'] = '';
				}

				
			} else {
				$values['next'][$key]['artist_bio'] = '';
				$values['next'][$key]['artist_image'] = '';
				$values['next'][$key]['song_lyrics'] = '';
			}
		}
			
		return $values;
	}
	
	/**
	/** MERGE ALL DATA IN ONE ARRAY function
	*/
	public function jazlerMerge($playNowUrl,$playHistoryUrl,$playNextUrl,$timezone,$apiSite){		
		//get all data functions
		$palyNow = $this->jazlerNowPlaying($playNowUrl,$timezone,$apiSite);//play now data
		$playHistory = $this->jazlerHistoryPlay($playHistoryUrl,$timezone,$apiSite);//play history data
		$nextHistory = $this->jazlerNextPlay($playNextUrl,$timezone,$apiSite);//play next data
		
		$results = array_merge($palyNow, $playHistory, $nextHistory);
		
		return $results;
	
	}
	
}






