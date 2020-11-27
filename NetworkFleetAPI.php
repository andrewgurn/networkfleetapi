<?php

/*
	Class: NetworkFleet
	Purpose: Manage API calls to Verizon's NetworkFleet service through cURL
	Author: Andrew Weitzel
	Github URL: https://github.com/andrewgurn/networkfleetapi
*/

class NetworkFleetAPI
{
	var $user;
	var $secret;
	var $tokenArray;
	var $token;
	var $tokenType = 'Bearer';
	var $refreshToken;
	var $defaultHost = 'api.networkfleet.com';
	var $defaultAuthHost = 'auth.networkfleet.com';
	/*
		The line below tells the API to return JSON data.  If you'd prefer XML data instead, replace it with:
		var $defaultContentType = 'application/vnd.networkfleet.api-v1+xml';
		
	*/
	var $defaultContentType = 'application/vnd.networkfleet.api-v1+json';

	function __construct($userIn, $secretIn, $tokenIn = '', $refreshTokenIn = '') 
	{
		$this->user = $userIn;
		$this->secret = $secretIn;
	
		//If we don't have a token already, create a new connection
		if($tokenIn == "")
		{
			apiConnect($userIn, $secretIn, '');
		}
		//If we already have a token, populate everything that would otherwise be populated by apiConnect().  No need to bother calling out again
		else
		{
			$this->token = $tokenIn;
			$this->tokenArray['access_token'] = $tokenIn;
			$this->refreshToken = $refreshTokenIn;
			$this->tokenArray['refreshToken'] = $refreshTokenIn;
			$this->tokenArray['tokenType'] = $tokenType;
		}
	}
	
	function apiConnect($userIn, $secretIn, $refreshTokenIn = '')
	{
		$host = $this->defaultAuthHost;
		$method = 'POST';
		$path = '/token';
		$url = urlencode("https://$host$path");

		if($refreshTokenIn != "")
		{
			$data = "grant_type=password&username=$userIn&password=$secretIn";
		}
		else
		{
			$data = "grant_type=refresh_token&refresh_token=$refreshTokenIn";
		}
		
		$options = array( CURLOPT_HTTPHEADER => array("Authorization: Basic czZCaGRSa3F0MzpnWDFmQmF0M2JW", "Content-Type: application/x-www-form-urlencoded"),
				  CURLOPT_POSTFIELDS => $data,
				  CURLOPT_HEADER => false,
				  CURLOPT_URL => $url,
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_SSL_VERIFYPEER => false);
		$curl = curl_init();
		curl_setopt_array($curl, $options);
		$json = curl_exec($curl);
		curl_close($curl);
		$this->tokenArray = json_decode($json, true);
		$this->token = $this->tokenArray ["access_token"];
		$this->tokenType = $this->tokenArray ["token_type"];
		$this->refreshToken = $this->tokenArray ["refresh_token"];
	}
	
	function getQuery($path, $method, $queryParameters = "")
	{
		$host = $this->defaultHost;
		
		if($method == 'GET' && $queryParameters != '')
		{
			$path .= $queryParameters;
		}
		
		$url = urlencode("https://$host$path");
		
		$options = array( CURLOPT_HTTPHEADER => array(
								"Authorization: ".$this->tokenType." ".$this->token." "
								, "Content-Type:".$this->defaultContentType
								, "Accept: ".$this->defaultContentType
							),
				  CURLOPT_HEADER => false,
				  CURLOPT_URL => $url,
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_SSL_VERIFYPEER => false);
		$curl = curl_init();
		curl_setopt_array($curl, $options);
		$json = curl_exec($curl);
		
		return $json;
	}
	
	//In case you need the results of the connection elsewhere
	function getTokenArray()
	{
		return $this->tokenArray;
	}

	//Calls the test API
	function testCall()
	{	
		return $this->getQuery('/test?', 'GET', '');
	}
	
	/*
		Find the location of all your vehicles, a specific vehicle, or a group of vehichles.  All search options are optional
		See the API docs for a decription of each:
		https://developer.networkfleet.com/resourceClass/index?resourcePath=locations 
	*/
	function getVehicleLocations($index = '', $limit = '', $forGroup = '', $forAttribute = '', $withLabel = '', $withVehicleID = '', $withVIN = '')
	{
		$queryParameters = '';

		if($index != ''){ $queryParameters .= "index=$index&"; }
		if($limit != ''){ $queryParameters .= "limit=$limit&"; }
		if($forGroup != ''){ $queryParameters .= "for-group=$forGroup&"; }
		if($forAttribute != ''){ $queryParameters .= "for-attribute=$forAttribute&"; }
		if($withLabel != ''){ $queryParameters .= "with-label=$withLabel&"; }
		if($withVehicleID != ''){ $queryParameters .= "with-vehicle-id=$withVehicleID&"; }
		if($withVIN != ''){ $queryParameters .= "with-vin=$withVIN&"; }
		
		return $this->getQuery('/locations?$queryParameters', 'GET','');
	}

	function getLandmarkCategories()
	{
		return $this->getQuery('/landmarkCategories?', 'GET', '');
	}

} //end class

?>
